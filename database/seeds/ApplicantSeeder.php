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
                'first_name' => 'Juan',
                'middle_name' => 'Santiago',
                'last_name' => 'Dela Cruz',
                'nickname' => 'juan',
                'nationality' => 'Filipino',
                'religion' => 'Iglesia Ni Cristo',
                'unit' => '3',
                'lot_block' => 'Lot 2 Blk 8',
                'street' => 'Camachile',
                'subdivision' => 'Bataan Homes',
                'municipality' => 'Balanga City',
                'barangay' => 'San Jose',
                'province' => 'Bataan',
                'zip' => '2100',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'contact_number' => '9455878983',
                'email' => 'Juan@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332442',
                'first_name' => 'Pepe',
                'middle_name' => '',
                'last_name' => 'Smith',
                'nickname' => 'pe',
                'nationality' => 'Filipino',
                'religion' => 'Catholic',
                'unit' => '3',
                'lot_block' => 'Lot 2 Blk 8',
                'street' => 'Camachile',
                'subdivision' => 'Bataan Homes',
                'municipality' => 'Balanga City',
                'barangay' => 'San Jose',
                'province' => 'Bataan',
                'zip' => '2100',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'contact_number' => '9455878983',
                'email' => 'pepe@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332443',
                'first_name' => 'Jackson',
                'middle_name' => '',
                'last_name' => 'Swift',
                'nickname' => 'jack',
                'nationality' => 'Filipino',
                'religion' => 'Catholic',
                'unit' => '3',
                'lot_block' => 'Lot 2 Blk 8',
                'street' => 'Camachile',
                'subdivision' => 'Bataan Homes',
                'municipality' => 'Balanga City',
                'barangay' => 'San Jose',
                'province' => 'Bataan',
                'zip' => '2100',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'contact_number' => '9455878983',
                'email' => 'Jackson@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332444',
                'first_name' => 'Taylor',
                'middle_name' => '',
                'last_name' => 'Swift',
                'nickname' => 'Taylor',
                'nationality' => 'Filipino',
                'religion' => 'Catholic',
                'unit' => '3',
                'lot_block' => 'Lot 2 Blk 8',
                'street' => 'Camachile',
                'subdivision' => 'Bataan Homes',
                'municipality' => 'Balanga City',
                'barangay' => 'San Jose',
                'province' => 'Bataan',
                'zip' => '2100',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
                'contact_number' => '9455878983',
                'email' => 'Taylor@yahoo.com'
            ],
            [ 
                'reference_no' => '7649332445',
                'first_name' => 'Rubi',
                'middle_name' => 'Evangelista',
                'last_name' => 'Rubiano',
                'nickname' => 'Ra',
                'nationality' => 'Filipino',
                'religion' => 'Catholic',
                'unit' => '3',
                'lot_block' => 'Lot 2 Blk 8',
                'street' => 'Camachile',
                'subdivision' => 'Bataan Homes',
                'municipality' => 'Balanga City',
                'barangay' => 'San Jose',
                'province' => 'Bataan',
                'zip' => '2100',
                'gender_id' => 1,
                'civil_status_id' => 1,
                'birthdate' => '2024-04-10',
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
                    'first_name' =>  $applicant['first_name'],
                    'middle_name' =>  $applicant['middle_name'],
                    'last_name' =>  $applicant['last_name'],
                    'nickname' => $applicant['nickname'],
                    'nationality' => $applicant['nationality'],
                    'religion' => $applicant['religion'],
                    'unit' => $applicant['unit'],
                    'lot_block' => $applicant['lot_block'],
                    'street' => $applicant['street'],
                    'subdivision' => $applicant['subdivision'],
                    'municipality' => $applicant['municipality'],
                    'barangay' => $applicant['barangay'],
                    'province' => $applicant['province'],
                    'zip' => $applicant['zip'],
                    'gender_id' => $applicant['gender_id'],
                    'civil_status_id' => $applicant['civil_status_id'],
                    'birthdate' => $applicant['birthdate'],
                    'contact_number' => $applicant['contact_number'],
                    'email' => $applicant['email'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $applicant['first_name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate applicant ' . $applicant['first_name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
