<?php

use Illuminate\Database\Seeder;
use App\Employee;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $applicants = [
            [ 
                'reference_no' => '7649332441',
                'name' => 'Juan Dela Cruz',
                'nickname' => 'juan',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'address' => '198 PUROK 3 ACAPULCO ST.',
                'contact_number' => '9455878983',
                'email' => 'Juan@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332442',
                'name' => 'Pepe Smith',
                'nickname' => 'pe',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'address' => '198 PUROK 3 ACAPULCO ST.',
                'contact_number' => '9455878983',
                'email' => 'pepe@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332443',
                'name' => 'Jackson Taylor Swift',
                'nickname' => 'jack',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'address' => '198 PUROK 3 ACAPULCO ST.',
                'contact_number' => '9455878983',
                'email' => 'Jackson@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332444',
                'name' => 'Taylor Swift',
                'nickname' => 'Taylor',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'address' => '198 PUROK 3 ACAPULCO ST.',
                'contact_number' => '9455878983',
                'email' => 'Taylor@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332445',
                'name' => 'Ra Rubiano',
                'nickname' => 'Ra',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'address' => '198 PUROK 3 ACAPULCO ST.',
                'contact_number' => '9455878983',
                'email' => 'Ra@yahoo.com'
            ],
        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($applicants as $key => $applicant) {

            try {
             
                // Create applicants
                $applicantsObj = Employee::create([
                    'reference_no' => $applicant['reference_no'],
                    'name' => $applicant['name'],
                    'nickname' => $applicant['nickname'],
                    'gender_id' => $applicant['gender_id'],
                    'civil_status_id' => $applicant['civil_status_id'],
                    'birthdate' => $applicant['birthdate'],
                    'address' => $applicant['address'],
                    'contact_number' => $applicant['contact_number'],
                    'email' => $applicant['email'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $applicant['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate applicant ' . $applicant['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
