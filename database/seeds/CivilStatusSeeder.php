<?php

use Illuminate\Database\Seeder;
use App\CivilStatus;

class CivilStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $civilStatuses = [
            [ 
                'name' => 'Single'
            ],
            [ 
                'name' => 'Married'
            ],
            [ 
                'name' => 'Widowed'
            ],
            [ 
                'name' => 'Legally Separated'
            ],
        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($civilStatuses as $key => $civilStatus) {

            try {
             
                // Create civilStatuses
                $civilStatusObj = CivilStatus::create([
                    'name' => $civilStatus['name'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $civilStatus['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Gender ' . $civilStatus['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
