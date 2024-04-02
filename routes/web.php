<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //if has a user redirect to home page if not redirect to login
    if (Auth::user()) return redirect()->route('home');

    return view('auth.login');
});

//register route disable
Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

Route::middleware('auth')->group(function () {
    //Dashboard
    Route::get('/dashboard', 'HomeController@index')->name('home');

    //Users
    Route::resource('/user', 'User\UserController');
    Route::post('user/fetch/q', 'User\UserFetchController@fetchUser')->name('activeUser');
    Route::post('inactive-user/fetch/q', 'User\UserFetchController@fetchInactiveUser')->name('InactiveUser');
    Route::get('user/destroy/{id}', 'User\UserController@destroy');
    Route::get('user/restore/{id}', 'User\UserController@restore');

    //Profile
    Route::get('/profile', 'User\ProfileController@viewProfile')->name('user-profile');
    Route::patch('/profile-update', 'User\ProfileController@updateProfile')->name('update-profile');
    Route::patch('/notification-update', 'User\ProfileController@updateNotification')->name('update-notification');

    //Logs
    Route::resource('logs', 'Logs\LogController');
});
