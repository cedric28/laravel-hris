<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\CustomerPoint;
use App\Sale;
use App\SaleItem;
use App\Inventory;
use App\Point;

class POSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventories = DB::table('inventories')
                        ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                        ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                        ->select('inventories.*', 'inventories.id as productId','categories.category_name')
                        ->get();
        return view("pos.index",[
            'inventories' => $inventories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'cart' => 'required|array',
                'cash_tendered' =>'required|numeric|gt:0'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'data' => $validator->errors(),
                    'message' => 'Your Cart or Cash Tendered is Empty'
                ], 422); 
            }

            $cart = $request->cart;
            $customerFullName = $request->customerFullName;
            $customerPointInfo = (array)$request->customer_point_info;
            $customerEarnerInfo = (array)$request->customer_point_earner;
            $discount = (array)$request->discount;
            $cashTendered = $request->cash_tendered;
            $totalPrice = 0;
            foreach($cart as $item){
                $totalPrice += $item['item_quantity'] * floatval($item['selling_price']);
            }

            $totalDiscount = !empty($discount) ? floatval($discount['discount_rate']) * $totalPrice : 0;
            $discountRate = !empty($discount) ? floatval($discount['discount_rate']) : 0;
            $totalAmountDue = $totalPrice - $totalDiscount;

            //check current user
            $user = \Auth::user()->id;

            $sales = new Sale();
            $sales->or_no = $this->generateUniqueCode();
            $sales->customer_fullname = ucwords($customerFullName) ?? "";
            $sales->total_price = number_format($totalPrice,2);
            $sales->discount_rate = $discountRate;
            $sales->total_discount = $totalDiscount;
            $sales->total_amount_due = $totalAmountDue;
            $sales->cash_tendered = floatval($cashTendered);
            $sales->cash_change = floatval($cashTendered) - $totalAmountDue;
            $sales->creator_id = $user;
            $sales->updater_id = $user;

            if($sales->save()){
                $discountPoint = Point::all();
                $pointEarned = (int)($totalAmountDue / $discountPoint[0]->price_per_point) * floatval($discountPoint[0]->point);
                if(!empty($customerPointInfo)){
                    //Points Deduction
                    $customerId = $customerPointInfo['id']; 
                    CustomerPoint::create([
                        'customer_id' => $customerId,
                        'sale_id' =>  $sales->id,
                        'point' => -floatval($discount['total_needed_point']),
                        'creator_id' => $user,
                        'updater_id' => $user
                    ]);   
                }

                if(!empty($customerEarnerInfo)){
                    $customerEarnerId = $customerEarnerInfo['id'];
                    if($pointEarned > 0){
                        CustomerPoint::create([
                            'customer_id' => $customerEarnerId,
                            'sale_id' =>  $sales->id,
                            'point' => $pointEarned,
                            'creator_id' => $user,
                            'updater_id' => $user
                        ]);   
                    }
                }

                //Save Items
                foreach ($cart as $key => $cartItem) {
                    SaleItem::create([
                        'sale_id' => $sales->id,
                        'inventory_id' => $cartItem['id'],
                        'item_sku' => $cartItem['sku'],
                        'item_name' => $cartItem['product_name'],
                        'price' => floatval($cartItem['selling_price']),
                        'quantity' => $cartItem['item_quantity'],
                        'creator_id' => $user,
                        'updater_id' => $user
                    ]);

                    $inventory = Inventory::withTrashed()->findOrFail($cartItem['id']);
                    $inventory->quantity = $inventory->quantity - $cartItem['item_quantity'];
                    $inventory->update(); 
                }
            }

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return response()->json([
                'sales' => $sales,
                'status' => 'success',
                'message' => 'Order Complete!'
            ], 200);

        } catch(\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return response()->json([
                'data' => $e->getMessage(),
                'message' => 'Order Failed'
            ], 500); 
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
        //
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
        //
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

    public function generateUniqueCode()
    {
        do {
            $or_no = 'TD'.random_int(1000000000, 9999999999);
        } while (Sale::where("or_no", "=", $or_no)->first());
  
        return $or_no;
    }
}
