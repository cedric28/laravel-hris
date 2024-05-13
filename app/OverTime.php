<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OverTime extends Model
{
    use SoftDeletes;
    
    public function deployment() {
        return $this->belongsTo(Deployment::class)->withTrashed();
    }
}
