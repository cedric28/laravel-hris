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
        $this->call(IndustrySeeder::class);
        $this->call(EmploymentTypeSeeder::class);
        $this->call(LeaveTypeSeeder::class);
        $this->call(GenderSeeder::class);
        $this->call(CivilStatusSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(LeaveStatusSeeder::class);
    }
}
