<?php

use Illuminate\Database\Seeder;
use App\Discount;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $discounts = [
            [ 
                'discount_name' => 'Senior/PWD',
                'discount_rate' => 0.20
            ] 

        ];
        
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

            
        foreach($discounts as $key => $discount) {

            try {
                $discountObj = Discount::create([

                    'discount_name' => $discount['discount_name'],
                    'discount_rate' => $discount['discount_rate'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $discountObj->discount_name . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Discount ' . $discountObj['discount_name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
