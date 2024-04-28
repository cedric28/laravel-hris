<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deployment extends Model
{
    use SoftDeletes;

    public function employee() {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function deployment_employment_type() {
        return $this->belongsTo(EmploymentType::class)->withTrashed();
    }

    public function client() {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class,'deployment_id','id')->withTrashed();
    }
}
