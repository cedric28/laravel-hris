<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DeliveryRequest;
use App\DeliveryRequestItem;
use App\Supplier;
use App\Product;
use Carbon\Carbon;
use Validator;

class DeliveryRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveryRequest = DeliveryRequest::all();
        $deliveryRequestItems = DeliveryRequestItem::all();
        return view('stock.delivery_request.index', [
            'deliveryRequest' => $deliveryRequest,
            'deliveryRequestItems' => $deliveryRequestItems
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
        return view("stock.delivery_request.create",[
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
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:255',
                'supplier_id' => 'required|integer',
                'delivery_at' => 'required|string|max:50',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;
            
            //save data in the delivery table
            $delivery = new DeliveryRequest();
            $delivery->reference_no = $this->generateUniqueCode();
            $delivery->content = $request->content;
            $delivery->delivery_at = Carbon::createFromFormat('m/d/Y', $request->delivery_at)->format('Y-m-d');
            $delivery->supplier_id = $request->supplier_id;
            $delivery->creator_id = $user;
            $delivery->updater_id = $user;
            $delivery->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('delivery-request.edit', $delivery->id);
         
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
        $deliveryRequest = DeliveryRequest::withTrashed()->findOrFail($id);
        $products = Product::all();
        $suppliers = Supplier::all();
        $deliveryRequestItem = DeliveryRequestItem::where('delivery_request_id',$id)->get();

        return view('stock.delivery_request.show', [
            'deliveryRequest' => $deliveryRequest,
            'products' => $products,
            'deliveryRequestItem' => $deliveryRequestItem,
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
        $deliveryRequest = DeliveryRequest::withTrashed()->findOrFail($id);
        $products = Product::all();
        $suppliers = Supplier::all();
        $deliveryRequestItem = DeliveryRequestItem::where('delivery_request_id',$id)->get();

        return view('stock.delivery_request.edit', [
            'deliveryRequest' => $deliveryRequest,
            'products' => $products,
            'deliveryRequestItem' => $deliveryRequestItem,
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
         /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {

            //check current User
            $user = \Auth::user();
            //check if the product data is exist,if not redirect to error page
            $delivery = DeliveryRequest::withTrashed()->findOrFail($id);
            //validate request value
            $validator = Validator::make($request->all(), [
                'reference_no' => 'required|string|unique:delivery_requests,reference_no,'.$delivery->id,
                'content' => 'required|string|max:255',
                'supplier_id' => 'required|integer',
                'delivery_at' => 'required|string|max:50',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //save data in the delivery table
            $delivery->reference_no = $request->reference_no;
            $delivery->content = $request->content;
            $delivery->delivery_at = Carbon::createFromFormat('m/d/Y', $request->delivery_at)->format('Y-m-d');
            $delivery->supplier_id = $request->supplier_id;
            $delivery->updater_id = $user->id;
            $delivery->update();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg","Delivery Request {$delivery->reference_no} Update Successfully");
         
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
        $delivery = DeliveryRequest::findOrFail($id);
        $delivery->delete();
    }

    public function generateUniqueCode()
    {
        do {
            $reference_no = 'DR'.random_int(1000000000, 9999999999);
        } while (DeliveryRequest::where("reference_no", "=", $reference_no)->first());
  
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
                'qty' => 'required|numeric|gt:0',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;
            
            //save data in the delivery table
            $stock = new DeliveryRequestItem();
            $stock->delivery_request_id = $request->delivery_request_id;
            $stock->product_id = $request->product_id;
            $stock->qty = $request->qty;
            $stock->creator_id = $user;
            $stock->updater_id = $user;
            $stock->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('delivery-request.edit', $stock->delivery_request_id)
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
        $stock = DeliveryRequestItem::findOrFail($id);
        $stock->delete();
    }
}
