<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gender extends Model
{
    use SoftDeletes;

    public function employees()
    {
        return $this->hasMany(Employee::class,'gender_id','id')->withTrashed();
    }
}
