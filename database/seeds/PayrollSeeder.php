<?php

use Illuminate\Database\Seeder;
use App\Payroll;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payrollDates = [];
        $startDate = Carbon::create(2023, 1, 1); // Start from January 2023
        $endDate = Carbon::create(2024, 12, 31); // End in December 2024

        // Generate payroll dates for 1st and 15th of each month from January 2023 to December 2024
        while ($startDate->lessThanOrEqualTo($endDate)) {
            // First half of the month payroll
            $payrollDates[] = [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $startDate->copy()->addDays(14)->format('Y-m-d'),
                'description' => $startDate->format('F') . ' 15th Payroll',
            ];

            // Second half of the month payroll
            $payrollDates[] = [
                'start_date' => $startDate->copy()->addDays(14)->format('Y-m-d'),
                'end_date' => $startDate->copy()->endOfMonth()->format('Y-m-d'),
                'description' => $startDate->format('F') . ' End of Month Payroll',
            ];

            // Move to the next month
            $startDate->addMonth();
        }

        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        foreach($payrollDates as $key => $payrollDate) {

            try {
                // Create payrollDate
                $payrollObj = Payroll::create([
                    'start_date' => $payrollDate['start_date'],
                    'end_date' => $payrollDate['end_date'],
                    'description' => $payrollDate['description'],
                    'creator_id' => 1,
                    'updater_id' => 1
                ]);

                echo $payrollDate['description'] . ' | ';

            } catch (Exception $e) {
                echo 'Duplicate Payroll Dates ' . $payrollDate['description'] . ' | ';
            }
        }

        echo "\n";

        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }
}