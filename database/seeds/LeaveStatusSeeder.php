<?php

use Illuminate\Database\Seeder;
use App\LeaveStatus;

class LeaveStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leavesStatuses = [
            [ 
                'name' => 'Approved'
            ],
            [ 
                'name' => 'Pending'
            ],
            [ 
                'name' => 'Cancel'
            ]

        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        foreach($leavesStatuses as $key => $status) {

            try {
             
                // Create 
                $statusObj = LeaveStatus::create([

                    'name' => $status['name'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $status['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate status ' . $status['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
