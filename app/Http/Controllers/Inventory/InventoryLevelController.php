<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\InventoryLevel;
use Carbon\Carbon;
use Validator;

class InventoryLevelController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $inventoryLevels = InventoryLevel::all();
        return view("inventory.level.index",[
            'inventoryLevels' => $inventoryLevels
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

        return view("inventory.level.create");
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
                'category_name' => 'required|string|max:50|unique:categories,category_name',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;
           
            //save category
            $category = new Category();
            $category->category_name = $request->category_name;
            $category->creator_id = $user;
            $category->updater_id = $user;
            $category->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('category.create')
                        ->with('successMsg','Category Save Successful');
         
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
         //prevent other user to access to this page
         $this->authorize("isAdmin");

        $inventoryLevel = InventoryLevel::withTrashed()->findOrFail($id);

        return view('inventory.level.show', [
            'inventoryLevel' => $inventoryLevel
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

        $inventoryLevel = InventoryLevel::withTrashed()->findOrFail($id);


        return view('inventory.level.edit', [
            'inventoryLevel' => $inventoryLevel
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
            //check category if exist
            $inventoryLevel = InventoryLevel::withTrashed()->findOrFail($id);

            $messages = [
                'lt' => 'The :attribute must be less than Critical Level.',
            ];
            $validator = Validator::make($request->all(), [
                'critical' => 'required|numeric|gt:0',
                're_stock' => 'required|numeric|gt:0|lt:critical',
            ], $messages);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;

            //save the update value
            $inventoryLevel->re_stock = $request->re_stock;
            $inventoryLevel->critical = $request->critical;
            $inventoryLevel->updater_id = $user;
            $inventoryLevel->update();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg","Inventory Level Update Successfully");
         
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
        //prevent other user to access to this page
        $this->authorize("isAdmin");

        //delete category
        $category = Category::findOrFail($id);
        $category->delete();
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

            $category = Category::onlyTrashed()->findOrFail($id);

            /* Restore category */
            $category->restore();
            \DB::commit();

            return back()->with("successMsg","Successfully Restore the data");

        } catch(\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
}
