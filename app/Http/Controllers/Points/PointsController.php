<?php

namespace App\Http\Controllers\Points;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Point;
use App\Log;
use Carbon\Carbon;
use Validator;

class PointsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $points = Point::all();
        return view("point.index", [
            'points' => $points
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

        return view("point.create");
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
                'point_name' => 'required|string|max:50|unique:points,point_name',
                'discount_rate' => 'required|numeric|gt:0',
                'point' => 'required|numeric|gt:0',
                'price_per_point' => 'required|numeric|gt:0',
                'total_needed_point' => 'required|numeric|gt:0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save point
            $point = new Point();
            $point->point_name = $request->point_name;
            $point->discount_rate = $request->discount_rate;
            $point->point = $request->point;
            $point->price_per_point = $request->price_per_point;
            $point->total_needed_point = $request->total_needed_point;
            $point->creator_id = $user;
            $point->updater_id = $user;
            $point->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('point.create')
                ->with('successMsg', 'Point Save Successful');
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

        $point = Point::withTrashed()->findOrFail($id);

        return view('point.show', [
            'point' => $point
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

        $point = Point::withTrashed()->findOrFail($id);


        return view('point.edit', [
            'point' => $point
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
            //check point if exist
            $point = Point::withTrashed()->findOrFail($id);

            //validate the request value
            $validator = Validator::make($request->all(), [
                'point_name' => 'required|string|unique:points,point_name,' . $point->id,
                'discount_rate' => 'required|numeric|gt:0',
                'point' => 'required|numeric|gt:0',
                'price_per_point' => 'required|numeric|gt:0',
                'total_needed_point' => 'required|numeric|gt:0',
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save the update value
            $point->point_name = $request->point_name;
            $point->discount_rate = $request->discount_rate;
            $point->point = $request->point;
            $point->price_per_point = $request->price_per_point;
            $point->total_needed_point = $request->total_needed_point;
            $point->updater_id = $user;
            $point->update();


            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " update point discount " . $point->point_name . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Point Update Successfully");
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

        //delete point
        $point = Point::findOrFail($id);
        $point->delete();
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

            $point = Point::onlyTrashed()->findOrFail($id);

            /* Restore point */
            $point->restore();
            \DB::commit();
            return back()->with("successMsg", "Successfully Restore the data");
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
}
