<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = ['deployment_id','attendance_time', 'attendance_out', 'attendance_date','day_of_week','status','hours_worked','creator_id','updater_id'];

    public function deployment() {
        return $this->belongsTo(Deployment::class)->withTrashed();
    }

    public function late()
    {
        return $this->hasOne(LateTime::class,'attendance_id','id')->withTrashed();
    }
}
