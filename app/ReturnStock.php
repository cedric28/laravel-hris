<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnStock extends Model
{
    use SoftDeletes;

    public function return_stock_items()
    {
        return $this->hasMany(ReturnStockItem::class,'return_stock_id')->withTrashed();
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
    }
}
