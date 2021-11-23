<?php

use Illuminate\Database\Seeder;
use App\Inventory;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventories = [
            [ 
                'product_id' => 1,
                'original_price' => 50,
                'selling_price' => 60,
                'quantity' => 50,
            ] ,
            [ 
                'product_id' => 2,
                'original_price' => 30,
                'selling_price' => 40,
                'quantity' => 50,
            ] ,
            [ 
                'product_id' => 3,
                'original_price' => 20.75,
                'selling_price' => 24.50,
                'quantity' => 50,
            ] ,
            [ 
                'product_id' => 4,
                'original_price' => 20,
                'selling_price' => 25,
                'quantity' => 50
            ] ,
            [ 
                'product_id' => 5,
                'original_price' => 10.75,
                'selling_price' => 12.50,
                'quantity' => 50,
            ] ,
            [ 
                'product_id' => 6,
                'original_price' => 10,
                'selling_price' => 25,
                'quantity' => 50,
            ] ,
            [ 
                'product_id' => 7,
                'original_price' => 5.50,
                'selling_price' => 6.75,
                'quantity' => 50,
            ] ,
            [ 
                'product_id' => 8,
                'original_price' => 15,
                'selling_price' => 25.50,
                'quantity' => 50
            ] ,
            [ 
                'product_id' => 9,
                'original_price' => 20,
                'selling_price' => 28.50,
                'quantity' => 50
            ]
        ];
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

            
        foreach($inventories as $key => $inventory) {

            try {
             
                // Create Inventory
                $inventoryObj = Inventory::create([

                    'product_id' => $inventory['product_id'],
                    'original_price' => $inventory['original_price'],
                    'selling_price' => $inventory['selling_price'],
                    'quantity' => $inventory['quantity'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $inventoryObj->product->product_name . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Inventory ' . $inventory['product_id'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
