<?php

use Illuminate\Database\Seeder;
use App\NotificationSetting;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();
        try {
            $notificationObj = NotificationSetting::create([
                'creator_id' => 1,
                'updater_id' => 1
            ]);
        } catch (Exception $e) {
        }

        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
