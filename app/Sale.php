<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    public function items(){
        return $this->hasMany(SaleItem::class, 'sale_id','id')->withTrashed();
    }
}
