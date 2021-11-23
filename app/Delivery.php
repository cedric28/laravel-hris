<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;

    public function stocks()
    {
        return $this->hasMany(Stock::class,'delivery_id')->withTrashed();
    }
}
