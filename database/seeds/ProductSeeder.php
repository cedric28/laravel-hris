<?php

use Illuminate\Database\Seeder;
use App\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [ 
                'sku' => '10035',
                'product_name' => 'Allerkid-drops-10ml-1',
                'generic_name' => 'CETIRIZINE DiHCL',
                'content' => 'Drops',
                'category_id' => 1
            ],
            [ 
                'sku' => '10036',
                'product_name' => 'Allerkid-syrup-30ml',
                'generic_name' => 'CETIRIZINE DiHCL',
                'content' => 'Drops',
                'category_id' => 1
            ],
            [ 
                'sku' => '10039',
                'product_name' => 'Alnix drops 10ml',
                'generic_name' => 'CETIRIZINE DiHCL',
                'content' => 'Drops',
                'category_id' => 1,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10048',
                'product_name' => 'Ambrolex 15mg syrup 60ml',
                'generic_name' => 'Ambroxol HCl',
                'content' => 'Syrup',
                'category_id' => 2,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10047',
                'product_name' => 'Ambrolex drops 15ml',
                'generic_name' => 'Ambroxol HCl',
                'content' => 'Drops',
                'category_id' => 2,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10102',
                'product_name' => 'Ascof 300mg syrup 120ml Ponkan',
                'generic_name' => 'Vitex Negundo L. Lagundi Leaf',
                'content' => 'Syrup',
                'category_id' => 3,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10108',
                'product_name' => 'Ascof Forte Syrup 120ml',
                'generic_name' => 'Vitex Negundo L. Lagundi Leaf',
                'content' => 'Syrup',
                'category_id' => 3,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10085',
                'product_name' => 'Asmalin Broncho Syrup 60ml',
                'generic_name' => 'Cefixime',
                'content' => 'Syrup',
                'category_id' => 4,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10044',
                'product_name' => 'Benadryl (Expectorant) syrup 60ml',
                'generic_name' => 'Guaifenesin',
                'content' => 'Syrup',
                'category_id' => 5,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10020',
                'product_name' => 'Bioflu Syrup',
                'generic_name' => 'Phenylephrine HCL 10mg + Chlorphenamine Maleate 2mg + Paracetamol 500mg',
                'content' => 'Syrup',
                'category_id' => 6,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10000',
                'product_name' => 'Biogesic 120mg Orange Syrup 60ml',
                'generic_name' => 'Paracetamol',
                'content' => 'Syrup',
                'category_id' => 7,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10055',
                'product_name' => 'Bisolvon 4mg syrup 120ml',
                'generic_name' => 'Bisolvon, Barkacin',
                'content' => 'Syrup',
                'category_id' => 2,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10087',
                'product_name' => 'Broncaire Expectorant Syrup 60ml',
                'generic_name' => 'Guaifenesin + Salbutamol',
                'content' => 'Syrup',
                'category_id' => 4,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10033',
                'product_name' => 'Celestamine syrup',
                'generic_name' => 'Betamethasone/Dexchlorpheniramine maleate 2',
                'content' => 'Syrup',
                'category_id' => 1,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10021',
                'product_name' => 'Disudrin drops 10ml',
                'generic_name' => 'Phenylephrine HCl and Chlorphenamine maleate',
                'content' => 'Drops',
                'category_id' => 6,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10097',
                'product_name' => 'Dynatussin Syrup 120ml',
                'generic_name' => 'Dextromethorphan Hydrobromide',
                'content' => 'Syrup',
                'category_id' => 8,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10059',
                'product_name' => 'Loviscol drops 15ml',
                'generic_name' => 'Carbocisteine',
                'content' => 'Drops',
                'category_id' => 2,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10109',
                'product_name' => 'Plemex For Kids 60ml',
                'generic_name' => 'Vitex Negundo L. Lagundi Leaf',
                'content' => 'Syrup',
                'category_id' => 9,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10017',
                'product_name' => 'Tempra 1-5 years 120ml Orange',
                'generic_name' => 'Paracetamol',
                'content' => 'Syrup',
                'category_id' => 7,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10092',
                'product_name' => 'Ventolin Expectorant Syrup 120ml',
                'generic_name' => 'Salbutamol + Guaifensin',
                'content' => 'Syrup',
                'category_id' => 10,
                'supplier_id' => 1
            ],
            [ 
                'sku' => '10080',
                'product_name' => 'Ventar Expectorant syrup 60ml',
                'generic_name' => 'Guaifenesin',
                'content' => 'Syrup',
                'category_id' => 5,
                'supplier_id' => 1
            ],
        ];
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        foreach($products as $key => $product) {
            try {
             
                // Create Product Medicine
                $medicineObj = Product::create([
                    'product_name' => $product['product_name'],
                    'generic_name' => $product['generic_name'],
                    'sku' => $product['sku'],
                    'content' => $product['content'],
                    'supplier_id' => 1,
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                $medicineObj->categories()->sync(1);

                echo $medicineObj->product_name . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Product ' . $product['product_name'] . ' | ';
            }   
        }

        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
