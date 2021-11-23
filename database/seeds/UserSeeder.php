<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [ 
                'first_name' => 'Administrator', 
                'last_name' => 'Administrator', 
                'email' => 'admin@admin.com', 
                'pw' => 'passw0rd',
                'role_id' => 1
            ],
            [ 
                'first_name' => 'Cashier', 
                'last_name' => 'Cashier', 
                'email' => 'cashier@cashier.com', 
                'pw' => 'passw0rd',
                'role_id' => 2
            ]
        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($users as $key => $user) {

            try {
             
                // Create Users
                $userObj = User::create([

                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'password' => bcrypt($user['pw']),
                    'role_id' => $user['role_id'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $user['email'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate email address ' . $user['email'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
