<?php

use Illuminate\Database\Seeder;
use App\InventoryAdjustmentType;

class InventoryAdjustmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventoryAdjustmentTypes = [
            [ 
                'type' => 'Add To Stock'
            ] ,
            [
                'type' => 'Remove from Stock'
            ]

        ];
        
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

            
        foreach($inventoryAdjustmentTypes as $key => $inventoryAdjustmentType) {

            try {
             
                // Create adjustment types
                $adjustmentTypesObj = InventoryAdjustmentType::create([

                    'type' => $inventoryAdjustmentType['type'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $adjustmentTypesObj->type . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Inventory Adjustment Type ' . $inventoryAdjustmentType['type'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
