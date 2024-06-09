<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Deployment;
use Carbon\Carbon;

class CheckAttendanceCommand extends Command
{
    protected $signature = 'attendance:check';

    protected $description = 'Check and insert attendance for new employees';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $newEmployees = Deployment::where('status', 'new')->get();

    foreach ($newEmployees as $employee) {
        $startDate = $employee->start_date;
        $endDate = $employee->end_date;

        // Check if the current date is within the employee's start and end dates
        $currentDate = Carbon::today();
        if ($currentDate->greaterThanOrEqualTo($startDate) && $currentDate->lessThanOrEqualTo($endDate)) {
            if (!$currentDate->isWeekend()) {
                // Check if the employee has attendance for today
                $attendanceToday = Attendance::where('deployment_id', $employee->id)
                ->whereDate('attendance_date', $currentDate)
                ->exists();

                if (!$attendanceToday) {
                    // Insert attendance if not present
                    $attendance = Attendance::create([
                        'attendance_time' => '00:00:00',
                        'attendance_out' => '00:00:00',
                        'attendance_date' =>$currentDate->format('Y-m-d'),
                        'deployment_id' => $employee->id,
                        'day_of_week' =>  $currentDate->dayOfWeek == 7 ? 0 : $attendanceDate->dayOfWeek + 1,
                        'hours_worked' => 0,
                        'status' => 'Absent',
                        'creator_id' => 1,
                        'updater_id' => 1
                    ]);
                }
            }
        }
    }

    $this->info('Attendance checked and updated for new employees.');
    }
}
