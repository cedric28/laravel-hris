<?php

namespace App\Http\Controllers\HolidaySetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use App\HolidaySetting;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\Rule;

class HolidaySettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = HolidaySetting::all();
        $inActiveHolidays = HolidaySetting::onlyTrashed()->get();

        return view("holiday-setting.index", [
            'holidays' => $holidays,
            'inActiveHolidays' => $inActiveHolidays
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
        return view("holiday-setting.create");
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
            $holidayDate = Carbon::parse($request->holiday)->format('Y-m-d');
             //validate request value
             $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('holiday_settings')
                    ->where(function ($query) use ($holidayDate) {
                        return $query->where('holiday', $holidayDate);
                    }),
                ],
                'holiday' => 'required|date|unique:holiday_settings,holiday',
                'percentage' => 'required|numeric',
            ]);
            
 
             if ($validator->fails()) {
                 return back()->withErrors($validator->errors())->withInput();
             }
             

             $existingHoliday = HolidaySetting::where('holiday', $holidayDate)->first();

            if ($existingHoliday) {
                return back()->withErrors(['holiday' => 'This holiday already exists.'])->withInput();
            }
             //check current user
             $user = \Auth::user()->id;
 
             //save client
             $holiday = new HolidaySetting();
             $holiday->name = $request->name;
             $holiday->holiday = $holidayDate;
             $holiday->percentage = $request->percentage;
             $holiday->creator_id = $user;
             $holiday->updater_id = $user;
             $holiday->save();
 
 
             $log = new Log();
             $log->log = "User " . \Auth::user()->email . " create holiday " . $holiday->id . " at " . Carbon::now();
             $log->creator_id =  \Auth::user()->id;
             $log->updater_id =  \Auth::user()->id;
             $log->save();
             /*
             | @End Transaction
             |---------------------------------------------*/
             \DB::commit();
 
             return redirect()->route('holiday-setting.create')
                 ->with('successMsg', 'Holiday Date Save Successful');
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
         $holiday = HolidaySetting::withTrashed()->findOrFail($id);
         $date = Carbon::parse($holiday->holiday)->format('m/d/y');
         return view('holiday-setting.edit', [
             'holiday' => $holiday,
             'date' =>   $date
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
            //check holiday setting if exist
            $holiday = HolidaySetting::withTrashed()->findOrFail($id);
            $holidayDate = Carbon::parse($request->holiday)->format('Y-m-d');
            // Determine validation rules based on whether the holiday is being updated
            $rules = [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('holiday_settings', 'name')->ignore($id),
                ],
                'percentage' => 'required|numeric',
            ];
            
            if ($request->has('holiday') && $request->holiday !== $holiday->holiday) {
                $rules['holiday'] = [
                    'required',
                    'string',
                    Rule::unique('holiday_settings', 'holiday')->ignore($id),
                ];
            }
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            // Manually check if the new holiday date already exists in the database
            if ($request->has('holiday') && $request->holiday !== $holiday->holiday) {
                $existingHoliday = HolidaySetting::where('holiday', $holidayDate)
                    ->where('id', '<>', $id) // Exclude the current record from the check
                    ->first();
            
                if ($existingHoliday) {
                    return back()->withErrors(['holiday' => 'This holiday already exists.'])->withInput();
                }
            }
            
            // Manually check if the new name already exists in the database
            $existingName = HolidaySetting::where('name', $request->input('name'))
                ->where('id', '<>', $id) // Exclude the current record from the check
                ->first();
            
            if ($existingName) {
                return back()->withErrors(['name' => 'This name already exists.'])->withInput();
            }
            

            //check current user
            $user = \Auth::user()->id;
          
 
            //save the update value
            $holiday->name = $request->name;
            $holiday->holiday = $holidayDate;
            $holiday->percentage = $request->percentage;
            $holiday->updater_id = $user;
            $holiday->update();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit holiday " . $holiday->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Holiday Date Update Successfully");
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
        $holiday = HolidaySetting::findOrFail($id);
        $holiday->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete holiday " . $holiday->id . " at " . Carbon::now();
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

            $holiday = HolidaySetting::onlyTrashed()->findOrFail($id);

            /* Restore holiday */
            $holiday->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore holiday " . $holiday->id . " at " . Carbon::now();
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
