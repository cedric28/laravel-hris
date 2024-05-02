<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //define user to perform a given action 
        Gate::define('isAdmin',function($user){
            return $user->role_id == 1;
        });

        Gate::define('isHR',function($user){
            return $user->role_id == 2;
        });
        
        Gate::define('isHROrAdmin',function($user){
            return $user->role_id == 2 || $user->role_id == 1;
        });
    }
}
