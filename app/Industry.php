<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Industry extends Model
{
    use SoftDeletes;

    
    public function industries()
    {
        return $this->hasMany(EmploymentHistory::class,'industry_id','id')->withTrashed();
    }
}
