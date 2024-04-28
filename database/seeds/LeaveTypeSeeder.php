<?php

use Illuminate\Database\Seeder;
use App\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leaveTypes = [
            [ 
                'name' => 'Vacation'
            ],
            [ 
                'name' => 'Sick'
            ],
            [ 
                'name' => 'Emergency'
            ]
        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($leaveTypes as $key => $leaveType) {

            try {
             
                // Create LeaveType
                $roleObj = LeaveType::create([

                    'name' => $leaveType['name'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $leaveType['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Leave Types ' . $leaveType['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
