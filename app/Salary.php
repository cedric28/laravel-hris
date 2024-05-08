<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;

    public function deployments()
    {
        return $this->hasMany(Deployment::class,'deployment_id','id')->withTrashed();
    }
}
