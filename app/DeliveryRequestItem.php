<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRequestItem extends Model
{
    use SoftDeletes;

    public function delivery_request() {
        return $this->belongsTo(DeliveryRequest::class)->withTrashed();
    }

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
