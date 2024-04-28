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
        $genders = [
            [ 
                'reference_no' => '7490505111',
                'name' => 'Samsung',
                'nickname' => 'sam',
                'address' => '198 PUROK 3 ACAPULCO ST',
                'contact_number' => '9455878983',
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
                    'short_name' => $client['nickname'],
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
