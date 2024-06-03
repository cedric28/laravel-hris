<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class AttendanceImport implements ToCollection, WithHeadingRow {

    public $data;

    public function collection(Collection $rows) {

        $xyz = $rows->toArray();
      
        $attendance = [];
        foreach ($rows as $row => $col) {
            $attendance[] = [
                'employee_no' => $col['employee_no'],
                'attendance_time' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($col['attendance_time']))->format('H:i:s'),
                'attendance_out' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($col['attendance_out']))->format('H:i:s'),
                'attendance_date' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($col['attendance_date']))->format('Y-m-d')
            ];
            $row++;
        }
        
        $this->data = $attendance;
        return $attendance;
    }

    public function headingRow(): int
    {
        return 1;
    }
}