<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryAdjustmentType extends Model
{
    use SoftDeletes;

    public function inventory_adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class,'inventory_adjustment_type_id','id')->withTrashed();
    }
}
