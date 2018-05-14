<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    //
    //黑名单
    protected $guarded = [];

    public function parent()
    {
        return $this->hasOne('App\Models\Agent', 'id', 'parent_id');
    }
}
