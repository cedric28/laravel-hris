<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use SoftDeletes;

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function delivery() {
        return $this->belongsTo(Delivery::class)->withTrashed();
    }
}
