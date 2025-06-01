<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'description',
        'start_date',
        'end_date',
        'creator_id',
        'updater_id',
    ];

    public function payslips()
    {
        return $this->hasMany(Payslip::class,'payroll_id','id')->withTrashed();
    }
}
