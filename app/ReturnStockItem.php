<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnStockItem extends Model
{
    use SoftDeletes;

    public function return_stock() {
        return $this->belongsTo(ReturnStock::class)->withTrashed();
    }

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
