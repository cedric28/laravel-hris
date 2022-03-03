<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryPerProduct extends Model
{
    use SoftDeletes;

    public function inventory() {
        return $this->belongsTo(Inventory::class, 'inventory_id')->withTrashed();
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id')->withTrashed();
    }
}
