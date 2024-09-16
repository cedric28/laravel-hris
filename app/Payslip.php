<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    use SoftDeletes;

    protected $fillable = ['deployment_id','payroll_id', 'holiday_pay', 'other_deduction','other_pay','creator_id','updater_id'];

    public function deployment() {
        return $this->belongsTo(Deployment::class)->withTrashed();
    }

    public function payroll() {
        return $this->belongsTo(Payroll::class)->withTrashed();
    }
}
