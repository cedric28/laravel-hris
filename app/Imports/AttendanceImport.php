<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToCollection, WithHeadingRow {

    public $data;

    public function collection(Collection $rows) {

        $xyz = $rows->toArray();
        
        $this->data = $xyz;
        return $xyz;
    }

    public function headingRow(): int
    {
        return 1;
    }
}