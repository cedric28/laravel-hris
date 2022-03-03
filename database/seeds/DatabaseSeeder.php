<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "\n";
        echo "/*---------------------------------------------- \n";
        echo "| @Populating Data! \n";
        echo "|----------------------------------------------*/ \n";

        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(InventoryAdjustmentTypeSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(DiscountSeeder::class);
        $this->call(PointSeeder::class);
        $this->call(InventoryLevelSeeder::class);
        $this->call(NotificationSettingSeeder::class);
    }
}
