<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ReturnStock;
use App\ReturnStockItem;
use App\Inventory;
use App\Supplier;
use Carbon\Carbon;
use Validator;

class ReturnStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $returnStocks = ReturnStock::all();
        $returnStockItems = ReturnStockItem::all();
        return view('stock.return.index', [
            'returnStocks' => $returnStocks,
            'returnStockItems' => $returnStockItems
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Inventory::all();
        $itemStatus = [
            ['status' => 'expired'],
            ['status' => 'damage'],
            ['status' => 'wrong item']
        ];
        return view("stock.return.create", [
            'suppliers' => $suppliers,
            'products' => $products,
            'item_status' => $itemStatus
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
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required|integer',
                'delivery_at' => 'required|string|max:50',
                'received_at' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save data in the delivery table
            $returnStock = new ReturnStock();
            $returnStock->reference_no = $this->generateUniqueCode();
            $returnStock->delivery_at = Carbon::createFromFormat('m/d/Y', $request->delivery_at)->format('Y-m-d');
            $returnStock->received_at = Carbon::createFromFormat('m/d/Y', $request->received_at)->format('Y-m-d');
            $returnStock->supplier_id = $request->supplier_id;
            $returnStock->creator_id = $user;
            $returnStock->updater_id = $user;
            $returnStock->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('return-stock.edit', $returnStock->id);
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
        $returnStock = ReturnStock::withTrashed()->findOrFail($id);
        $products = Inventory::all();
        $suppliers = Supplier::all();
        $returnStockItems = ReturnStockItem::where('return_stock_id', $id)->get();

        return view('stock.return.show', [
            'returnStock' => $returnStock,
            'products' => $products,
            'returnStockItems' => $returnStockItems,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $returnStock = ReturnStock::withTrashed()->findOrFail($id);
        $products = Inventory::all();
        $suppliers = Supplier::all();
        $itemStatus = [
            ['status' => 'expired'],
            ['status' => 'damage'],
            ['status' => 'wrong item']
        ];
        $returnStockItems = ReturnStockItem::where('return_stock_id', $id)->get();

        return view('stock.return.edit', [
            'returnStock' => $returnStock,
            'products' => $products,
            'returnStockItems' => $returnStockItems,
            'suppliers' => $suppliers,
            'itemStatus' => $itemStatus
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
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {

            //check current User
            $user = \Auth::user();
            //check if the product data is exist,if not redirect to error page
            $returnStock = ReturnStock::withTrashed()->findOrFail($id);
            //validate request value
            $validator = Validator::make($request->all(), [
                'reference_no' => 'required|string|unique:return_stocks,reference_no,' . $returnStock->id,
                'supplier_id' => 'required|integer',
                'delivery_at' => 'required|string|max:50',
                'received_at' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //save data in the delivery table
            $returnStock->reference_no = $request->reference_no;
            $returnStock->delivery_at = Carbon::createFromFormat('m/d/Y', $request->delivery_at)->format('Y-m-d');
            $returnStock->received_at = Carbon::createFromFormat('m/d/Y', $request->received_at)->format('Y-m-d');
            $returnStock->supplier_id = $request->supplier_id;
            $returnStock->updater_id = $user->id;
            $returnStock->update();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Return Stock {$returnStock->reference_no} Update Successfully");
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
        //delete delivery
        $returnStock = ReturnStock::findOrFail($id);
        $returnStock->delete();
    }

    public function generateUniqueCode()
    {
        do {
            $reference_no = 'DR' . random_int(1000000000, 9999999999);
        } while (ReturnStock::where("reference_no", "=", $reference_no)->first());

        return $reference_no;
    }

    public function addProduct(Request $request)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //validate request value
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'remark' => 'nullable',
                'note' => 'sometimes|required_without:remark',
                'qty' => 'required|numeric|gt:0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save data in the delivery table
            $stock = ReturnStockItem::firstOrNew(
                [
                    'return_stock_id' => $request->return_stock_id,
                    'product_id' =>  $request->product_id,
                    'creator_id' => $user,
                    'updater_id' => $user
                ]
            );

            $stock->remark = $request->remark;
            $stock->note = $request->note;
            $stock->qty += $request->qty;
            $stock->save();
            // $stock = new ReturnStockItem();
            // $stock->return_stock_id = $request->return_stock_id;
            // $stock->product_id = $request->product_id;
            // $stock->qty = $request->qty;
            // $stock->note = $request->note;
            // $stock->creator_id = $user;
            // $stock->updater_id = $user;
            // $stock->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('return-stock.edit', $stock->return_stock_id)
                ->with('successMsg', 'Product Data Save Successful');
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    public function removeProduct($id)
    {
        //delete product
        $stock = ReturnStockItem::findOrFail($id);
        $stock->delete();
    }
}
