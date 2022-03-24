<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Log;
use App\Customer;
use Carbon\Carbon;
use Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();
        $InactiveCustomer = Customer::onlyTrashed()->get();
        return view("customer.index", [
            'customers' => $customers,
            'InactiveCustomer' => $InactiveCustomer
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

        return view("customer.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //prevent other user to access to this page
        $this->authorize("isAdmin");
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //validate request value
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:customers,name',
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save customer
            $customer = new Customer();
            $customer->reference_no = $this->generateUniqueCode();
            $customer->name = $request->name;
            $customer->address = $request->address;
            $customer->contact_number = $request->contact_number;
            $customer->email = $request->email;
            $customer->creator_id = $user;
            $customer->updater_id = $user;
            $customer->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create customer " . $customer->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('customer.create')
                ->with('successMsg', 'Customer Save Successful');
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
        //prevent other user to access to this page
        $this->authorize("isAdmin");

        $customer = Customer::withTrashed()->findOrFail($id);
        $totalPoints = $customer->customer_points->sum('point');

        return view('customer.show', [
            'customer' => $customer,
            'totalPoints' => $totalPoints
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
        //prevent other user to access to this page
        $this->authorize("isAdmin");

        $customer = Customer::withTrashed()->findOrFail($id);


        return view('customer.edit', [
            'customer' => $customer
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
            //check customer if exist
            $customer = Customer::withTrashed()->findOrFail($id);

            //validate the request value
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:customers,name,' . $customer->id,
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50'
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save the update value
            $customer->name = $request->name;
            $customer->address = $request->address;
            $customer->contact_number = $request->contact_number;
            $customer->email = $request->email;
            $customer->updater_id = $user;
            $customer->update();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit customer " . $customer->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Customer Update Successfully");
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
        //prevent other user to access to this page
        $this->authorize("isAdmin");

        //delete category
        $customer = Customer::findOrFail($id);
        $customer->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete customer " . $customer->reference_no . " at " . Carbon::now();
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

            $customer = Customer::onlyTrashed()->findOrFail($id);

            /* Restore customer */
            $customer->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore customer " . $customer->reference_no . " at " . Carbon::now();
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

    public function generateUniqueCode()
    {
        do {
            $reference_no = random_int(1000000000, 9999999999);
        } while (Customer::where("reference_no", "=", $reference_no)->first());

        return $reference_no;
    }
}
