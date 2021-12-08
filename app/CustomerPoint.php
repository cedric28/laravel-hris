<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPoint extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'customer_id', 'sale_id', 'point', 'creator_id','updater_id', 
    ];


    public function customer() {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function sale() {
        return $this->belongsTo(Sale::class)->withTrashed();
    }
}
