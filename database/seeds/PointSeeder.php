<?php

use Illuminate\Database\Seeder;
use App\Point;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pointsCategories = [
            [ 
                'point_name' => 'Customer Point',
                'discount_rate' => 0.50,
                'point' => 1,
                'price_per_point' => 200,
                'total_needed_point' => 1000
            ] 
        ];
        
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

            
        foreach($pointsCategories as $key => $category) {

            try {
                $pointsObj = Point::create([

                    'point_name' => $category['point_name'],
                    'discount_rate' => $category['discount_rate'],
                    'point' => $category['point'],
                    'price_per_point' => $category['price_per_point'],
                    'total_needed_point' => $category['total_needed_point'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $pointsObj->point_name . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Point Category ' . $pointsObj['point_name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
