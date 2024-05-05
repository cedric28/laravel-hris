<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes;

    public function leaves()
    {
        return $this->hasMany(Leave::class,'leave_type_id','id')->withTrashed();
    }

}
