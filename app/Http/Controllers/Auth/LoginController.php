<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Log;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo()
    {
        $currentUser = \Auth::user()->role_id;
        $now = Carbon::now();
        \DB::beginTransaction();
        try {
            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " log on " . $now;
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }

        if ($currentUser == 1) {
            return RouteServiceProvider::HOME;
        } else {
            return RouteServiceProvider::SCHEDULE;
        }
    }
}
