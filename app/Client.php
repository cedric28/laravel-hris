<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    public function deployments()
    {
        return $this->hasMany(Deployments::class,'client_id','id')->withTrashed();
    }
}
