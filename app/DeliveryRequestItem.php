<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRequestItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_request_id', 'product_id', 'creator_id','updater_id', 
    ];

    public function delivery_request() {
        return $this->belongsTo(DeliveryRequest::class)->withTrashed();
    }

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
