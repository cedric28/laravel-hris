<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Category;
use Carbon\Carbon;
use Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $categories = Category::all();
        return view("category.index",[
            'categories' => $categories
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

        return view("category.create");
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

        $category = Category::withTrashed()->findOrFail($id);

        return view('category.show', [
            'category' => $category
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

        $category = Category::withTrashed()->findOrFail($id);


        return view('category.edit', [
            'category' => $category
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
            $category = Category::withTrashed()->findOrFail($id);

            //validate the request value
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|unique:categories,category_name,'.$category->id
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            //check current user
            $user = \Auth::user()->id;

            //save the update value
            $category->category_name = $request->category_name;
            $category->updater_id = $user;
            $category->update();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg","Category Update Successfully");
         
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
