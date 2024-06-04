<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;

    public function deployments()
    {
        return $this->belongsTo(Deployment::class)->withTrashed();
    }
}
