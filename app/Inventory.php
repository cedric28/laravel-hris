<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_id'
    ];

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }
    
    public function sale_items(){
        return $this->hasMany(SaleItem::class, 'inventory_id','id')->withTrashed();
    }

    public function product_adjustments(){
        return $this->hasMany(InventoryAdjustment::class, 'inventory_id','id')->withTrashed();
    }
}
