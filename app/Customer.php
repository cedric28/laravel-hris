<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    public function customer_points()
    {
        return $this->hasMany(CustomerPoint::class,'customer_id','id')->withTrashed();
    }

    public function total()
    {
        return $this->customer_points->map(function ($i){
            return $i->point;
        })->sum();
    }
}
