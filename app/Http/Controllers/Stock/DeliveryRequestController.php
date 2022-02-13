<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DeliveryRequest;
use App\DeliveryRequestItem;
use App\Supplier;
use App\Product;
use App\Inventory;
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
        $deliveryStatus = [
            ['status' => 'cancel'],
            ['status' => 'pending'],
            ['status' => 'completed']
        ];
        return view("stock.delivery_request.create", [
            'suppliers' => $suppliers,
            'deliveryStatus' => $deliveryStatus
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
                // 'status' => 'required|in:cancel,pending,completed',
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
            // $delivery->status = $request->status;
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
        $deliveryRequest = DeliveryRequest::withTrashed()->findOrFail($id);
        $products = Product::all();
        $suppliers = Supplier::all();
        $deliveryRequestItem = DeliveryRequestItem::where('delivery_request_id', $id)->get();

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
        $deliveryRequestItem = DeliveryRequestItem::where('delivery_request_id', $id)->get();
        $deliveryStatus = [
            ['status' => 'cancel'],
            ['status' => 'pending'],
            ['status' => 'completed']
        ];

        return view('stock.delivery_request.edit', [
            'deliveryRequest' => $deliveryRequest,
            'products' => $products,
            'deliveryRequestItem' => $deliveryRequestItem,
            'suppliers' => $suppliers,
            'deliveryStatus' => $deliveryStatus
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
                'reference_no' => 'required|string|unique:delivery_requests,reference_no,' . $delivery->id,
                'content' => 'required|string|max:255',
                'status' => 'required|in:cancel,pending,completed',
                'supplier_id' => 'required|integer',
                'delivery_at' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            if ($request->status == "completed") {
                //validate request value
                $validator = Validator::make($request->all(), [
                    'vehicle' => 'required|string|max:50',
                    'vehicle_plate' => 'required|string|max:50',
                    'driver_name' => 'required|string|max:50',
                    'contact_number' => 'required|digits:10',
                    'received_by' => 'required|string|max:50',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator->errors())->withInput();
                }
            } else if ($request->status == "cancel") {
                $validator = Validator::make($request->all(), [
                    'reason_for_cancel' => 'required|string|max:50'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator->errors())->withInput();
                }
            }
            //save data in the delivery table
            $delivery->reference_no = $request->reference_no;
            $delivery->content = $request->content;
            $delivery->status = $request->status;
            $delivery->received_by = $request->received_by;
            $delivery->vehicle = $request->vehicle;
            $delivery->vehicle_plate = $request->vehicle_plate;
            $delivery->driver_name = $request->driver_name;
            $delivery->contact_number = $request->contact_number;
            $delivery->reason_for_cancel = $request->reason_for_cancel;
            $delivery->delivery_at = Carbon::createFromFormat('m/d/Y', $request->delivery_at)->format('Y-m-d');
            $delivery->supplier_id = $request->supplier_id;
            $delivery->updater_id = $user->id;
            if ($delivery->update()) {
                if ($delivery->status === "completed") {
                    $id = $delivery->id;
                    $deliveryItems = DeliveryRequestItem::where("delivery_request_id", $id)->get();
                    $totalReceivedQty = $deliveryItems->sum('received_qty');
                    $totalDefectivedQty = $deliveryItems->sum('defectived_qty');
                    if ($totalReceivedQty <= 0 && $totalDefectivedQty <= 0) {
                        return back()->with('delete', 'Please Update Received and Defective Quantity or Add Product before changing the status to COMPLETED');
                    }

                    foreach ($deliveryItems as $key => $value) {
                        if ($value->received_qty > 0) {
                            $inventory = Inventory::firstOrNew([
                                'product_id' => $value->product_id
                            ]);

                            $inventory->quantity = ($inventory->quantity + $value->received_qty);;
                            $inventory->creator_id = $user->id;
                            $inventory->updater_id = $user->id;
                            $inventory->save();
                        }
                    }
                }
            }
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Delivery Request {$delivery->reference_no} Update Successfully");
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
        $delivery = DeliveryRequest::findOrFail($id);
        $delivery->delete();
    }

    public function generateUniqueCode()
    {
        do {
            $reference_no = 'DR' . random_int(1000000000, 9999999999);
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
                'qty' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save data in the delivery table
            $stock = DeliveryRequestItem::firstOrNew([
                'delivery_request_id' => $request->delivery_request_id,
                'product_id' => $request->product_id
            ]);
            $stock->qty = ($stock->qty + $request->qty);
            $stock->note = $request->note;
            $stock->creator_id = $user;
            $stock->updater_id = $user;
            $stock->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('delivery-request.edit', $stock->delivery_request_id)
                ->with('successMsg', 'Product Data Save Successful');
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    public function updateProduct(Request $request)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            $messages = [
                'received_qty.required' => 'The Received Quantity field is required.',
                'defectived_qty.required' => 'The Defective Quantity field is required.',
                'expired_at.required' => 'The Expiration Date field is required.',
                'note.required' => 'The Note field is required.',

            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'received_qty' => 'required|integer',
                'defectived_qty' => 'required|integer',
                'expired_at' => 'required|string|max:50',
                'note' => 'required|string|max:50',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'data' => $validator->errors(),
                    'message' => ''
                ], 422);
            }

            //check current user
            $user = \Auth::user()->id;

            $deliveryItem = DeliveryRequestItem::findOrFail($request->product_id);

            $totalItems = $request->received_qty + $request->defectived_qty;

            if ($totalItems > $deliveryItem->qty) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Received and Defective Quantity must be equal to Request Quantity'
                ], 200);
            }

            $deliveryItem->received_qty = (int)$request->received_qty;
            $deliveryItem->defectived_qty = (int)$request->defectived_qty;
            $deliveryItem->note = $request->note;
            $deliveryItem->expired_at = Carbon::createFromFormat('m/d/Y', $request->expired_at)->format('Y-m-d');
            $deliveryItem->updater_id = $user;
            $deliveryItem->save();

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'deliveryItem' => $deliveryItem,
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return response()->json([
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function removeProduct($id)
    {
        //delete product
        $stock = DeliveryRequestItem::findOrFail($id);
        $stock->delete();
    }
}
