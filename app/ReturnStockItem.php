<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnStockItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'return_stock_id', 'product_id', 'qty', 'creator_id', 'updater_id'
    ];

    public function return_stock()
    {
        return $this->belongsTo(ReturnStock::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Inventory::class)->withTrashed();
    }
}
