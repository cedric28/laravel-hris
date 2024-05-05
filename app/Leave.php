<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use SoftDeletes;

    public function deployment() {
        return $this->belongsTo(Deployment::class)->withTrashed();
    }

    
    public function leave_type() {
        return $this->belongsTo(LeaveType::class)->withTrashed();
    }
}
