<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;
use App\InventoryLevel;
use App\InventoryAdjustmentType;
use App\InventoryAdjustment;
use App\Category;
use App\Supplier;
use App\CategoryPerProduct;
use App\SaleItem;
use App\Log;
use Validator;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventories = Inventory::all();
        $InactiveInventories = Inventory::onlyTrashed()->get();

        $inventoryLevel = InventoryLevel::all();
        return view("inventory.index", [
            'inventories' => $inventories,
            'inventoryLevel' => $inventoryLevel,
            'InactiveInventories' => $InactiveInventories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //prevent other user to access to this page
        $this->authorize("isAdmin");
        $products = Inventory::all();
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view("inventory.create", [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //validate request value
            $messages = [
                'lt' => 'The :attribute must be less than Selling price.',
            ];
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:50|unique:inventories,product_name',
                'generic_name' => 'required|string|max:50',
                'sku' => 'required|numeric|unique:inventories,sku',
                'content' => 'required|string|max:255',
                'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
                'supplier_id' => 'required|integer',
                'category_id' => 'required|integer',
                'selling_price' => 'required|numeric|gt:0',
                'original_price' => 'required|numeric|gt:0|lt:selling_price',
                'quantity' => 'required|numeric|gt:0'
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;


            $originalImage = $request->file('image');
            $photo = "";
            if ($originalImage) {
                $photo = time() . $originalImage->getClientOriginalName();
            }


            //save data in the inventory table
            $inventory = new Inventory();
            // $inventory->product_id = $request->product_id;
            $inventory->product_name = $request->product_name;
            $inventory->generic_name = $request->generic_name;
            $inventory->sku = $request->sku;
            $inventory->content = $request->content;
            $inventory->image = $photo;
            $inventory->supplier_id = $request->supplier_id;
            $inventory->original_price = $request->original_price;
            $inventory->selling_price = $request->selling_price;
            $inventory->quantity = $request->quantity;
            $inventory->creator_id = $user;
            $inventory->updater_id = $user;
            if ($inventory->save()) {
                $log = new Log();
                $log->log = "User " . \Auth::user()->email . " create product " . $inventory->product_name . " at " . Carbon::now();
                $log->creator_id =  \Auth::user()->id;
                $log->updater_id =  \Auth::user()->id;
                $log->save();
                if ($photo) {
                    $photoPath = public_path('images/' . $inventory->id . '/');

                    if (!file_exists($photoPath)) {
                        mkdir($photoPath, 0777, true);
                    }
                    // create instance
                    $img = \Image::make($originalImage->getRealPath());

                    // resize image to fixed size
                    $img->resize(100, 100);
                    $img->save($photoPath . $photo);
                }
            }

            $inventory->categories()->sync($request->category_id);
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('inventory.create')
                ->with('successMsg', 'Inventory Save Successful');
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $inventory = Inventory::withTrashed()->findOrFail($id);
        $inventoryAdjustmentTypes = InventoryAdjustmentType::all();
        $product_category = CategoryPerProduct::where('product_id', $inventory->id)->pluck('category_id')->all();

        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('inventory.edit', [
            'inventory' => $inventory,
            'inventoryAdjustmentTypes' => $inventoryAdjustmentTypes,
            'categoryId' => $product_category[0],
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //prevent other user to access to this page
        $this->authorize("isAdmin");

        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //check inventory if exist
            $inventory = Inventory::withTrashed()->findOrFail($id);

            //validate the request value
            $messages = [
                'lt' => 'The :attribute must be less than Selling price.',
                'product_name.unique' => 'The Product ' . $inventory->product_name . ' has already been taken.',
                'product_name.required' => 'The Product field is required.'
            ];
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|unique:inventories,product_name,' . $inventory->id,
                'generic_name' => 'required|string|max:50',
                'sku' => 'required|numeric|unique:inventories,sku,' . $inventory->id,
                'content' => 'required|string',
                'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
                'category_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'quantity' => 'numeric:gt:0',
                'selling_price' => 'required|numeric|gt:0',
                'original_price' => 'required|numeric|gt:0|lt:selling_price',
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save the update value
            $originalImage = $request->file('image');
            $currentPhoto = $inventory->image;
            $photo = "";
            if ($originalImage) {
                $photo = time() . $originalImage->getClientOriginalName();
                $productPhoto = public_path('images/' . $inventory->id . '/') . $currentPhoto;
                $photoPath = public_path('images/' . $inventory->id . '/');
                if (!file_exists($productPhoto)) {
                    mkdir($photoPath, 0777, true);
                } else {
                    @unlink($productPhoto);
                }
                // create instance
                $img = \Image::make($originalImage->getRealPath());

                // resize image to fixed size
                $img->resize(100, 100);
                $img->save($photoPath . $photo);
            } else {
                $photo = $currentPhoto;
            }

            $inventory->product_name = $request->product_name;
            $inventory->generic_name = $request->generic_name;
            $inventory->sku = $request->sku;
            $inventory->content = $request->content;
            $inventory->image = $photo;
            $inventory->supplier_id = $request->supplier_id;
            $inventory->selling_price = $request->selling_price;
            $inventory->original_price = $request->original_price;
            $inventory->updater_id = $user;

            if ($request->adjust_checker == 1) {
                $qtyLessThanOnHand = "";
                $newTotalQty = 0;
                if ($request->inventory_adjustment_type_id == 2) {
                    if ($request->adjusted_quantity > $inventory->quantity) {
                        $qtyLessThanOnHand = '|lte:quantity';
                    } else {
                        $newTotalQty = $inventory->quantity - $request->adjusted_quantity;
                    }
                } else {
                    $newTotalQty = $inventory->quantity + $request->adjusted_quantity;
                }
                $messagesAdjustment = [
                    'adjust_checker.required' => 'The Adjust Stock Checker field is required.',
                    'inventory_adjustment_type_id.required' => 'The Inventory Adjustment Type field is required.',
                    'reason.required' => 'The Reason field is required.',
                    'adjusted_quantity.required' => 'The Stock Adjusted Quantity field is required',
                    'adjusted_quantity.lte' => 'The adjusted quantity must be less than or equal to Stock on Hand.'

                ];

                $validatorInventoryAdjustment = Validator::make($request->all(), [
                    'adjust_checker' => 'required|integer',
                    'inventory_adjustment_type_id' => 'required|integer',
                    'adjusted_quantity' => 'required|numeric|gt:0' . $qtyLessThanOnHand,
                    'reason' => 'required|string|max:255'
                ], $messagesAdjustment);

                if ($validatorInventoryAdjustment->fails()) {
                    return back()->withErrors($validatorInventoryAdjustment->errors())->withInput();
                }

                $inventory->quantity = $newTotalQty;

                $inventoryAdjustment = new InventoryAdjustment();
                $inventoryAdjustment->inventory_id = $inventory->id;
                $inventoryAdjustment->adjusted_quantity = $request->adjusted_quantity;
                $inventoryAdjustment->inventory_adjustment_type_id = $request->inventory_adjustment_type_id;
                $inventoryAdjustment->reason = $request->reason;
                $inventoryAdjustment->creator_id = $user;
                $inventoryAdjustment->updater_id = $user;
                $inventoryAdjustment->save();

                $adjustmentType = InventoryAdjustmentType::findOrFail($request->inventory_adjustment_type_id);

                $log = new Log();
                $log->log = "User " . \Auth::user()->email . " adjust product " . $inventory->product_name . " as " . $adjustmentType->type . " with a quantity of " . $inventoryAdjustment->adjusted_quantity . " at " . Carbon::now();
                $log->creator_id =  \Auth::user()->id;
                $log->updater_id =  \Auth::user()->id;
                $log->save();
            }

            $inventory->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " update product " . $inventory->product_name . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            $inventory->categories()->sync($request->category_id);
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Inventory Update Successfully");
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //delete product
        $product = Inventory::findOrFail($id);
        $product->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete product " . $product->product_name . " at " . Carbon::now();
        $log->creator_id =  \Auth::user()->id;
        $log->updater_id =  \Auth::user()->id;
        $log->save();
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        \DB::beginTransaction();
        try {

            $product = Inventory::onlyTrashed()->findOrFail($id);

            /* Restore product */
            $product->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore product " . $product->product_name . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            \DB::commit();

            return back()->with("successMsg", "Successfully Restore the data");
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    public function productAdjustmentLogs(Request $request)
    {
        $inventoryAdjustments = InventoryAdjustment::all();
        return view("inventory.history.index", [
            'inventoryAdjustments' => $inventoryAdjustments
        ]);
    }

    public function salesLog(Request $request)
    {
        $salesLogs = SaleItem::all();
        return view("inventory.sales_logs.index", [
            'salesLogs' => $salesLogs
        ]);
    }
}
