<?php

namespace App\Http\Controllers\GeneralDeduction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use App\GeneralDeduction;
use Carbon\Carbon;
use Validator;

class GeneralDeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imagePath = public_path('assets/img/logo.png');
        $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
        $currentUser = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
        return view("general-deduction.index", [
            'base64Logo'=> $base64Logo,
            'currentUser' => $currentUser
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
           $this->authorize("isHROrAdmin");

           return view("general-deduction.create");
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
         $this->authorize("isHROrAdmin");
         /*
         | @Begin Transaction
         |---------------------------------------------*/
         \DB::beginTransaction();
 
         try {
             //validate request value
             $validator = Validator::make($request->all(), [
                 'name' => 'required|string|max:50|unique:general_deductions,name',
                 'amount' => 'required|numeric|min:0.01'
             ]);
 
             if ($validator->fails()) {
                 return back()->withErrors($validator->errors())->withInput();
             }
 
             //check current user
             $user = \Auth::user()->id;
 
        
 
             //save client
             $deduction = new GeneralDeduction();
             $deduction->name = $request->name;
             $deduction->amount = $request->amount;
             $deduction->creator_id = $user;
             $deduction->updater_id = $user;
             $deduction->save();
            
             $log = new Log();
             $log->log = "User " . \Auth::user()->email . " create general deductions " . $deduction->name . " at " . Carbon::now();
             $log->creator_id =  \Auth::user()->id;
             $log->updater_id =  \Auth::user()->id;
             $log->save();
             /*
             | @End Transaction
             |---------------------------------------------*/
             \DB::commit();
 
             return redirect()->route('general-deductions.create')
                 ->with('successMsg', 'General Deduction '. $deduction->name .' Save Successful');
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
          //prevent other user to access to this page
          $this->authorize("isHROrAdmin");

          $deduction = GeneralDeduction::withTrashed()->findOrFail($id);
  
  
          return view('general-deduction.edit', [
              'deduction' => $deduction
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
        $this->authorize("isHROrAdmin");

        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //check client if exist
            $deduction = GeneralDeduction::withTrashed()->findOrFail($id);

            //validate the request value
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:general_deductions,name,' . $deduction->id,
                'amount' => 'required|numeric|min:0.01'
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;


            //save the update value
            $deduction->name = $request->name;
            $deduction->amount = $request->amount;
            $deduction->updater_id = $user;
            $deduction->update();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit general deduction " . $deduction->name . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "General Deduction Update Successfully");
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
        $this->authorize("isHROrAdmin");

        //delete category
        $deduction = GeneralDeduction::findOrFail($id);
        $deduction->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete general deduction " . $deduction->name . " at " . Carbon::now();
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

            $deduction = GeneralDeduction::onlyTrashed()->findOrFail($id);

            /* Restore client */
            $deduction->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore general deduction " . $deduction->name . " at " . Carbon::now();
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
}
