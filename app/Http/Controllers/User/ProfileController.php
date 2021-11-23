<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Hash, DB;

class ProfileController extends Controller
{
    public function viewProfile()
    {
        $user = \Auth::user();

        return view('users.editprofile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            $user = \Auth::user();
            
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'email' => 'required|email|unique:users,email,'.$user->id,  
                'password' => 'same:confirm-password'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            if ( !$request->password == '')
            {
                $user->password = bcrypt($request->password);
            }
            
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->save();
        
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with('successMsg','User Data update Successfully');

        } catch(\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
}
