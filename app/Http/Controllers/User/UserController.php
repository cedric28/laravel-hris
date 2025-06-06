<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Log;
use App\Role;
use Validator, Hash, DB;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $InactiveUsers = User::onlyTrashed()->get();
        $imagePath = public_path('assets/img/logo.png');
        $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
        $currentUser = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
        return view('users.index', [
            'users' => $users,
            'InactiveUsers' => $InactiveUsers,
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
        $this->authorize("isAdmin");

        $roles = Role::all();

        return view("users.create", [
            'roles' => $roles
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
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'email' => 'required|email|unique:users,email',
                'hint' => 'required|max:50',
                // 'password' => 'required|same:confirm-password',
                'role_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            $password = Hash::make(12345);

            //check current user
            $currenUser = \Auth::user()->id;

            //save user
            $user = new User();
            $user->first_name = $request->first_name;
            $user->hint = $request->hint;
            $user->last_name = $request->last_name;
            $user->password = $password;
            $user->email = $request->email;
            $user->role_id = $request->role_id;
            $user->creator_id = $currenUser;
            $user->updater_id = $currenUser;
            $user->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " add user " .  $user->email . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('user.create')
                ->with('successMsg', 'User Data Save Successful');
        } catch (\Exception $e) {
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
        $user = User::withTrashed()->findOrFail($id);

        return view('users.show', [
            'user' => $user
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
        $user = User::withTrashed()->findOrFail($id);
        $roles = Role::all();

        return view("users.edit", [
            'roles' => $roles,
            'user' => $user
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
            $user = User::withTrashed()->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'hint' => 'required|max:50',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'same:confirm-password',
                'role_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            if (!$request->password == '') {
                $user->password = bcrypt($request->password);
            }

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->hint = $request->hint;
            $user->email = $request->email;
            $user->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit user " .  $user->email . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('user.edit', $user->id)
                ->with('successMsg', 'User Data update Successfully');
        } catch (\Exception $e) {
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
        //delete user
        $user = User::findOrFail($id);
        $user->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete user " .  $user->email . " at " . Carbon::now();
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

            $user = User::onlyTrashed()->findOrFail($id);

            /* Restore user */
            $user->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore user " .  $user->email . " at " . Carbon::now();
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
