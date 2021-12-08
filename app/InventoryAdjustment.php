<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryAdjustment extends Model
{
    use SoftDeletes;

    public function inventory() {
        return $this->belongsTo(Inventory::class)->withTrashed();
    }

    public function inventory_adjustment_type() {
        return $this->belongsTo(InventoryAdjustmentType::class)->withTrashed();
    }
}
