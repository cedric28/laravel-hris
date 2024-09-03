<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Hash, DB;
use App\NotificationSetting;
use Carbon\Carbon;
use App\Log;

class ProfileController extends Controller
{
    public function viewProfile()
    {
        $user = \Auth::user();

        return view('users.editprofile', [
            'user' => $user,
        ]);
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
                'hint' => 'required|max:50',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'same:confirm-password'
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
            $log->log = "User " . \Auth::user()->email . " update profile " .  $user->email . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with('successMsg', 'User Data update Successfully');
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    public function updateNotification(Request $request)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            $user = \Auth::user();

            $notificationSetup = NotificationSetting::withTrashed()->findOrFail(1);
            $notificationSetup->deliver_schedule_notif = $request->deliver_schedule_notif ?? 0;
            $notificationSetup->near_expiry_notif = $request->near_expiry_notif ?? 0;
            $notificationSetup->updater_id = $user->id;
            $notificationSetup->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " update notification settings at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with('successMsg', 'Notification update Successfully');
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
}
