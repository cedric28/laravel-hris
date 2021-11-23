<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRequest extends Model
{
    use SoftDeletes;

    public function delivery_request_items()
    {
        return $this->hasMany(DeliveryRequestItem::class,'delivery_request_id')->withTrashed();
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
    }
}
