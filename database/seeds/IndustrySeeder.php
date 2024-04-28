<?php

use Illuminate\Database\Seeder;
use App\Industry;

class IndustrySeeder extends Seeder
{
     /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $industries = [
            [ 
                'name' => 'Personal, Wellness, Beautification, Leisure Services'
            ],
            [ 
                'name' => 'Pharmaceuticals'
            ],
            [
                'name' => 'Public Services and National Defsense'
            ],
            [
                'name' => 'Real Estate'    
            ],
            [
                'name' => 'Remittance'
            ],
            [
                'name' => 'Specialized Professionals'
            ],
            [
                'name' =>'Transportation and Logistics'
            ],
            [
                'name' => 'Trust Entities'
            ],
            [
                'name' => 'Utilities and Sanitation'
            ],
            [
                'name' => 'Wholesale and Retail'
            ],
            [
                'name' => 'Management and Consultancy'
            ],
            [
                'name' => 'Manpower Services(Constractuals,Agency,Household)'
            ],
            [
                'name' => 'Manufacturing and Production'
            ],
            [
                'name'=>'Media and Journalism'
            ],
            [
                'name' => 'Mining and quarrying'
            ],
            [
                'name' => 'Multi-level Marketing'
            ],
            [
                'name' => 'Non-Profit, Charity, and Social Work'
            ],
            [
                'name' => 'Pawnshop'
            ],
            [
                'name' => 'Food and Retail'
            ],
            [
                'name' => 'Forex Trading/Money Changer'
            ],
            [
                'name' => 'General labor'
            ],
            [
                'name' => 'Goverment Employee'
            ],
            [
                'name' => 'Healthcare and Medical Services'
            ],
            [
                'name' => 'Hospital and Tourism'
            ],
            [
                'name' => 'Human Resources'
            ],
            [
                'name' => 'Insurance'
            ],
            [
                'name' => 'IT and Technical Services'
            ],
            [
                'name' => 'Jewelry Trading'
            ],
            [
                'name' => 'Legal Practice'
            ],
            [
                'name' => 'Academic and Researches'
            ],
            [
                'name' => 'Arts and Creatives'
            ],
            [
                'name' => 'Banking'
            ],
            [
                'name' => 'Call Center'
            ],
            [
                'name' => 'Car Dealership'
            ],
            [
                'name' => 'Casino/Gambling'
            ],
            [
                'name' => 'Construction and Engineering'
            ],
            [
                'name' => 'Education'
            ],
            [
                'name' => 'Farming, Fishing, Forestry'
            ],
            [
                'name' => 'Fashion and Entertainment'
            ],
            [
                'name' => 'Finance and Accounting'
            ],

        ];


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        foreach($industries as $key => $industry) {

            try {
                // Create Industry
                $industryObj = Industry::create([

                    'name' => $industry['name'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $industry['name'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate industry ' . $industry['name'] . ' | ';
            }   
        }

        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
