<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentType extends Model
{
    use SoftDeletes;

    public function employment_types()
    {
        return $this->hasMany(EmploymentHistory::class,'employment_type_id','id')->withTrashed();
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class,'employment_type_id','id')->withTrashed();
    }
}
