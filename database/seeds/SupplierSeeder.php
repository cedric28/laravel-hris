<?php

use Illuminate\Database\Seeder;
use App\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            [ 
                'name' => 'R8 Drugs Distributor',
                'short_name' => 'R8 Drugs',
                'address' =>  'Rizal Ave, Santa Cruz, Manila, 1014 Metro Manila',
                'contact_number' => '9389036501',
                'email' => 'r8drugs@gmail.com'
            ]
        ];

         /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        foreach($suppliers as $key => $supplier) {
            try {
                $supplierObj = Supplier::create([

                    'name' => $supplier['name'],
                    'short_name' => $supplier['short_name'],
                    'address' => $supplier['address'],
                    'contact_number' => $supplier['contact_number'],
                    'email' => $supplier['email'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $supplierObj->name . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Supplier ' . $supplier['name'] . ' | ';
            }   
        }

        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
