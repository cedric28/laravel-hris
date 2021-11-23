<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_id', 'inventory_id', 'item_sku', 'item_name', 'price', 'quantity', 'creator_id', 'updater_id'
    ];

    public function inventory() {
        return $this->belongsTo(Inventory::class)->withTrashed();
    }

    public function sale() {
        return $this->belongsTo(Sale::class)->withTrashed();
    }
}
