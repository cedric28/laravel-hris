<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    public function sale_items()
    {
        return $this->hasMany(SaleItem::class, 'inventory_id', 'id')->withTrashed();
    }

    public function product_adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'inventory_id', 'id')->withTrashed();
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category', 'category_per_products', 'product_id', 'category_id')->withTimestamps()->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
    }

    public function deliveryRequestItems()
    {
        return $this->hasMany(DeliveryRequestItem::class, 'product_id', 'id')->withTrashed();
    }

    public function returnStockItems()
    {
        return $this->hasMany(ReturnStockItem::class, 'product_id', 'id')->withTrashed();
    }
}
