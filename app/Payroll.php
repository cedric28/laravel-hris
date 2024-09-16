<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;

    public function payslips()
    {
        return $this->hasMany(Payslip::class,'payroll_id','id')->withTrashed();
    }
}
