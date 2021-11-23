<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;
use App\InventoryLevel;
use App\Product;
use App\InventoryAdjustmentType;
use App\InventoryAdjustment;
use Validator;

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
        $inventoryLevel = InventoryLevel::all();
        return view("inventory.index",[
            'inventories' => $inventories,
            'inventoryLevel' => $inventoryLevel
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
        $products = Product::all();

         return view("inventory.create",[
            'products' => $products
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
                'product_id' => 'required|integer|unique:inventories,product_id',
                'selling_price' => 'required|numeric|gt:0',
                'original_price' => 'required|numeric|gt:0|lt:selling_price',
                'quantity' => 'required|numeric|gt:0'
            ], $messages);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;
            
            //save data in the inventory table
            $inventory = new Inventory();
            $inventory->product_id = $request->product_id;
            $inventory->original_price = $request->original_price;
            $inventory->selling_price = $request->selling_price;
            $inventory->quantity = $request->quantity;
            $inventory->creator_id = $user;
            $inventory->updater_id = $user;
            $inventory->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('inventory.create')
                            ->with('successMsg','Inventory Save Successful');
         
        } catch(\Exception $e) {
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
        $products = Product::all();
        $inventoryAdjustmentTypes = InventoryAdjustmentType::all();

        return view('inventory.edit', [
            'inventory' => $inventory,
            'products' => $products,
            'inventoryAdjustmentTypes' => $inventoryAdjustmentTypes
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
                'product_id.unique' => 'The Product '. $inventory->product->product_name .' has already been taken.',
                'product_id.required' => 'The Product field is required.'
            ];
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|unique:inventories,product_id,'.$inventory->id,
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
            $inventory->product_id = $request->product_id;
            $inventory->selling_price = $request->selling_price;
            $inventory->original_price = $request->original_price;
            $inventory->updater_id = $user;
        
            if($request->adjust_checker == 1){
                $qtyLessThanOnHand = "";
                $newTotalQty = 0;
                if($request->inventory_adjustment_type_id == 2){
                    if($request->adjusted_quantity > $inventory->quantity){
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
                    'adjusted_quantity' => 'required|numeric|gt:0'.$qtyLessThanOnHand,
                    'reason' => 'required|string|max:255'
                ],$messagesAdjustment);

                if ($validatorInventoryAdjustment->fails()) {
                    return back()->withErrors($validatorInventoryAdjustment->errors())->withInput();
                }

                $inventory->quantity = $newTotalQty;
                $inventory->save();

                $inventoryAdjustment = new InventoryAdjustment();
                $inventoryAdjustment->inventory_id = $inventory->id;
                $inventoryAdjustment->inventory_adjustment_type_id = $request->inventory_adjustment_type_id;
                $inventoryAdjustment->reason = $request->reason;
                $inventoryAdjustment->creator_id = $user;
                $inventoryAdjustment->updater_id = $user;
                $inventoryAdjustment->save();
            }
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg","Inventory Update Successfully");
         
        } catch(\Exception $e) {
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
        //
    }
}
