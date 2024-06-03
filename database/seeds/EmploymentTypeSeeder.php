<?php

use Illuminate\Database\Seeder;
use App\EmploymentType;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employmentTypes = [
            [ 
                'name' => 'Part-time'
            ],
            [ 
                'name' => 'Self-employed'
            ],
            [ 
                'name' => 'Internship'
            ],
            [ 
                'name' => 'Contract'
            ],
            [ 
                'name' => 'Freelance'
            ]
        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($employmentTypes as $key => $employmentType) {

            try {
             
                // Create EmploymentType
                $employmentTypeObj = EmploymentType::create([

                    'name' => $employmentType['name'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $employmentType['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate employmentType ' . $employmentType['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
