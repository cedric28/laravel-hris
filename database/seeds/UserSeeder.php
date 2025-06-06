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
                'first_name' => 'Super Administrator', 
                'last_name' => 'Super Administrator', 
                'email' => 'admin@admin.com', 
                'pw' => 'passw0rd',
                'hint' => 'Super Administrator',
                'role_id' => 1
            ],
            [ 
                'first_name' => 'HR', 
                'last_name' => 'HR', 
                'email' => 'hr@hr.com', 
                'pw' => 'passw0rd',
                'hint' => 'HR',
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
                    'hint' => $user['hint'],
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
