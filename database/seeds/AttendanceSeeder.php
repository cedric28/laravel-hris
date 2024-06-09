<?php

use Illuminate\Database\Seeder;
use App\Attendance;

class AttendanceSeeder extends Seeder
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

        $currentMonth = '6';
        $currentYear = '2023';
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = strtotime("$currentYear-$currentMonth-$day");
            $dayOfWeek = date('N', $date);
            if ($dayOfWeek < 6) { // 6 represents Saturday, 7 represents Sunday
                
                try {
                
                    // Create
                    $civilStatusObj = Attendance::create([
                        'deployment_id' => 1,
                        'attendance_time' => '07:00:00',
                        'attendance_out' => '16:00:00',
                        'attendance_date' => date('Y-m-d', $date),
                        'day_of_week' => $dayOfWeek,
                        'status' => 'Present',
                        'hours_worked' => 8,
                        'creator_id' => 1,
                        'updater_id' => 1
                    ]);

                    echo date('Y-m-d', $date) . ' | ';

                } catch (Exception $e) {
                    echo 'Duplicate Attendance ' . date('Y-m-d', $date) . ' | ';
                }   
            }
        }
       
        echo "\n";


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}
