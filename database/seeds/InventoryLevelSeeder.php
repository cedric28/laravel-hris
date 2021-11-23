<?php

use Illuminate\Database\Seeder;
use App\InventoryLevel;

class InventoryLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventoryLevels = [
            [ 
                're_stock' => 5,
                'critical' => 5
            ]
        ];

         /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        foreach($inventoryLevels as $key => $level) {
            try {
                $levelObj = InventoryLevel::create([
                    're_stock' => $level['re_stock'],
                    'critical' => $level['critical'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

            } catch (Exception $e) {
                
            }   
        }

        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
