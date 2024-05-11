<?php

use Illuminate\Database\Seeder;
use App\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = [
            [ 
                'reference_no' => '7490505111',
                'name' => 'A-Movement',
                'short_name' => 'a-movement',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878984',
                'email' => 'sample@yahoo.com'
            ],
            [ 
                'reference_no' => '7490505222',
                'name' => 'Shopee Express Philippines',
                'short_name' => 'shopee',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878985',
                'email' => 'sample@yahoo.com'
            ],
            [ 
                'reference_no' => '7490505333',
                'name' => 'STT Philippines',
                'short_name' => 'stt phil',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878985',
                'email' => 'sample@yahoo.com'
            ],
            [ 
                'reference_no' => '7490505333',
                'name' => 'PHESI',
                'short_name' => 'phesi',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878985',
                'email' => 'sample@yahoo.com'
            ],
            [ 
                'reference_no' => '7490505444',
                'name' => 'Sutherland Global Solution',
                'short_name' => 'SGS',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878985',
                'email' => 'sample@yahoo.com'
            ],
            [ 
                'reference_no' => '7490505555',
                'name' => 'SM Sto. Tomas Batangas',
                'short_name' => 'SM STB',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878985',
                'email' => 'sample@yahoo.com'
            ],
            [ 
                'reference_no' => '7490505666',
                'name' => 'Suzuki Philippines',
                'short_name' => 'Suzuki',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878985',
                'email' => 'sample@yahoo.com'
            ]
        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($clients as $key => $client) {

            try {
             
                // Create Client
                $clientObj = Client::create([
                    'reference_no' => $client['reference_no'],
                    'name' => $client['name'],
                    'short_name' => $client['short_name'],
                    'address' => $client['address'],
                    'contact_number' => $client['contact_number'],
                    'email' => $client['email'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $client['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Client ' . $client['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
