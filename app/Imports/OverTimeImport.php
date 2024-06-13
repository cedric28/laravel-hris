<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;

class OverTimeImport implements ToCollection, WithHeadingRow, SkipsEmptyRows {

    public $data;

    public function collection(Collection $rows) {

        $xyz = $rows->toArray();
      
        $overtime = [];
        foreach ($rows as $row => $col) {
            $overtime[] = [
                'employee_no' => $col['employee_no'],
                'overtime_in' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($col['overtime_in']))->format('H:i:s'),
                'overtime_out' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($col['overtime_out']))->format('H:i:s'),
                'overtime_date' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($col['overtime_date']))->format('Y-m-d')
            ];
            $row++;
        }
        
        $this->data = $overtime;
        return $overtime;
    }

    public function headingRow(): int
    {
        return 1;
    }
}