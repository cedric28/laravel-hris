<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationalBackground extends Model
{
    use SoftDeletes;

    protected $casts = [
        'from' => 'Y-m-d', // Change your format
        'to' => 'Y-m-d',
    ];

    protected $fillable = [
        'employee_id'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class)->withTrashed();
    }
}
