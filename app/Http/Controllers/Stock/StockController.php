<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Stock;
use App\Delivery;
use App\Product;
use Carbon\Carbon;
use Validator;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveries = Delivery::all();
        $stocks = Stock::all();
        return view('stock.index', [
            'deliveries' => $deliveries,
            'stocks' => $stocks
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("stock.create");
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
                'reference_no' => 'required|string|max:50|unique:deliveries,reference_no',
                'vehicle' => 'required|string|max:50',
                'vehicle_plate' => 'required|string|max:50',
                'driver_name' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'received_by' => 'required|string|max:50',
                'received_at' => 'required|string|max:50',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;
            
            //save data in the delivery table
            $delivery = new Delivery();
            $delivery->reference_no = $request->reference_no;
            $delivery->received_by = $request->received_by;
            $delivery->vehicle = $request->vehicle;
            $delivery->vehicle_plate = $request->vehicle_plate;
            $delivery->driver_name = $request->driver_name;
            $delivery->contact_number = $request->contact_number;
            $delivery->received_at = Carbon::createFromFormat('m/d/Y', $request->received_at)->format('Y-m-d');;
            $delivery->creator_id = $user;
            $delivery->updater_id = $user;
            $delivery->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('stock.edit', $delivery->id);
         
        } catch(\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }   
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
                'qty' => 'required|numeric|gt:0',
                'expired_at' => 'required|string|max:50',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;
            
            //save data in the delivery table
            $stock = new Stock();
            $stock->delivery_id = $request->delivery_id;
            $stock->product_id = $request->product_id;
            $stock->qty = $request->qty;
            $stock->expired_at = Carbon::createFromFormat('m/d/Y', $request->expired_at)->format('Y-m-d');
            $stock->creator_id = $user;
            $stock->updater_id = $user;
            $stock->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('stock.edit', $stock->delivery_id)
                        ->with('successMsg','Product Data Save Successful');
         
        } catch(\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        } 
    }

    public function removeProduct($id)
    {
        //delete product
        $stock = Stock::findOrFail($id);
        $stock->delete();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $delivery = Delivery::withTrashed()->findOrFail($id);
        $products = Product::all();
        $stocks = Stock::where('delivery_id',$id)->get();

        return view('stock.show', [
            'delivery' => $delivery,
            'products' => $products,
            'stocks' => $stocks
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
        $delivery = Delivery::withTrashed()->findOrFail($id);
        $products = Product::all();
        $stocks = Stock::where('delivery_id',$id)->get();
        
        return view('stock.edit', [
            'delivery' => $delivery,
            'products' => $products,
            'stocks' => $stocks
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
            $delivery = Delivery::withTrashed()->findOrFail($id);
            //validate request value
            $validator = Validator::make($request->all(), [
                'reference_no' => 'required|string|unique:deliveries,reference_no,'.$delivery->id,
                'vehicle' => 'required|string|max:50',
                'vehicle_plate' => 'required|string|max:50',
                'driver_name' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'received_by' => 'required|string|max:50',
                'received_at' => 'required|string|max:50',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //save data in the delivery table
            $delivery->reference_no = $request->reference_no;
            $delivery->received_by = $request->received_by;
            $delivery->vehicle = $request->vehicle;
            $delivery->vehicle_plate = $request->vehicle_plate;
            $delivery->driver_name = $request->driver_name;
            $delivery->contact_number = $request->contact_number;
            $delivery->received_at = Carbon::createFromFormat('m/d/Y', $request->received_at)->format('Y-m-d');;
            $delivery->updater_id = $user->id;
            $delivery->update();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg","Stock {$delivery->reference_no} Update Successfully");
         
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
        //delete delivery
        $delivery = Delivery::findOrFail($id);
        $delivery->delete();
    }
}
