<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CivilStatus extends Model
{
    use SoftDeletes;

    public function employees()
    {
        return $this->hasMany(Employee::class,'civil_status_id','id')->withTrashed();
    }
}
