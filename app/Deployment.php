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

    public function leaves()
    {
        return $this->hasMany(Leave::class,'deployment_id','id')->withTrashed();
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class,'deployment_id','id')->withTrashed();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class,'deployment_id','id')->withTrashed();
    }

    public function overtimes()
    {
        return $this->hasMany(OverTime::class,'deployment_id','id')->withTrashed();
    }

    public function salary() {
        return $this->hasOne(Salary::class,'deployment_id','id')->withTrashed();
    }

    public function lates()
    {
        return $this->hasMany(LateTime::class,'deployment_id','id')->withTrashed();
    }
}
