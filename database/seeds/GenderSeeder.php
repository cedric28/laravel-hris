<?php

use Illuminate\Database\Seeder;
use App\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genders = [
            [ 
                'name' => 'Male'
            ],
            [ 
                'name' => 'Female'
            ],
        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($genders as $key => $gender) {

            try {
             
                // Create Gender
                $genderObj = Gender::create([

                    'name' => $gender['name'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $gender['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Gender ' . $gender['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
