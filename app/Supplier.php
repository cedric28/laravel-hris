<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    public function products()
    {
        return $this->hasMany(Inventory::class, 'supplier_id', 'id')->withTrashed();
    }

    public function return_stocks()
    {
        return $this->hasMany(ReturnStock::class, 'supplier_id', 'id')->withTrashed();
    }

    public function delivery_requests()
    {
        return $this->hasMany(DeliveryRequest::class, 'supplier_id', 'id')->withTrashed();
    }
}
