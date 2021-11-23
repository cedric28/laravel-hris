<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [ 
                'category_name' => 'ANTIHISTAMINE'
            ] ,
            [
                'category_name' => 'MUCOLYTIC'
            ],
            [
                'category_name' => 'COUGH REMEDYYTIC'
            ],
            [
                'category_name' => 'ANTI-ASTHMA + EXPECTORANT'
            ],
            [
                'category_name' => 'EXPECTORANT'
            ],
            [
                'category_name' => 'DECONGESTANT'
            ],
            [
                'category_name' => 'ANALGESIC'
            ],
            [
                'category_name' => 'EXPECTORANT + ANTITUSSIVE + DECONGESTANT'
            ],
            [
                'category_name' => 'ANTI-COUGH + ANTI-ASTHMA'
            ],
            [
                'category_name' => 'BRONCHODILATOR + EXPECTORANT'
            ]

        ];
        
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

            
        foreach($categories as $key => $category) {

            try {
             
                // Create Category
                $categoryObj = Category::create([

                    'category_name' => $category['category_name'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $categoryObj->category_name . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate category ' . $category['category_name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
