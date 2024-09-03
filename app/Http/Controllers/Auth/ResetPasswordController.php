<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\User;
use App\Log;
use App\Role;
use Validator, Hash, DB;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    public function index()
    {
        return view("auth.passwords.reset");
    }


    public function resetPassword(Request $request)
    {
       /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {

            $validator = Validator::make($request->all(), [
                'hint' => [
                    'required',
                    'max:50',
                    function ($attribute, $value, $fail) {
                        // Check if the hint exists in the users table
                        $exists = DB::table('users')->where('hint', $value)->exists();
                        if (!$exists) {
                            $fail('The provided hint does not exist.');
                        }
                    },
                ],
                'email' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) {
                        // Check if the email exists in the users table
                        $exists = DB::table('users')->where('email', $value)->exists();
                        if (!$exists) {
                            $fail('The provided email does not exist.');
                        }
                    },
                ],
                'password' => 'required_with:confirm-password|same:confirm-password',
            ]);
            
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            $user = User::where('email', $request->email)->first();
            // Only update the password if it is not empty and has been confirmed
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            $user->email = $request->email;
            $user->save();

            // $log = new Log();
            // $log->log = "User " . \Auth::user()->email . " edit user " .  $user->email . " at " . Carbon::now();
            // $log->creator_id =  ;
            // $log->updater_id =  \Auth::user()->id;
            // $log->save();

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('resetPage')->with('successMsg', 'Success resetting the password');
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
}
