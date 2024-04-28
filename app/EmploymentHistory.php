<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentHistory extends Model
{
    use SoftDeletes;

    protected $casts = [
        'start_date' => 'Y-m-d', // Change your format
        'end_date' => 'Y-m-d',
    ];

    protected $fillable = [
        'employee_id'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function employment_type() {
        return $this->belongsTo(EmploymentType::class)->withTrashed();
    }

    public function industry() {
        return $this->belongsTo(Industry::class)->withTrashed();
    }

}
