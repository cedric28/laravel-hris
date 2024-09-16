<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    use SoftDeletes;

    public function deployment() {
        return $this->belongsTo(Deployment::class)->withTrashed();
    }

    public function payroll() {
        return $this->belongsTo(Payroll::class)->withTrashed();
    }
}
