<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }
    
    public function sale_items(){
        return $this->hasMany(SaleItem::class, 'inventory_id','id')->withTrashed();
    }
}
