<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    public function deployments()
    {
        return $this->hasMany(Deployments::class,'employee_id','id')->withTrashed();
    }

    public function employment_histories()
    {
        return $this->hasMany(EmploymentHistory::class,'employee_id','id')->withTrashed();
    }

    public function educ_backgrounds()
    {
        return $this->hasMany(EducationalBackground::class,'employee_id','id')->withTrashed();
    }
}
