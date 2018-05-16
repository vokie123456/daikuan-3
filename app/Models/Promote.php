<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promote extends Model
{
    //
    protected $guarded = [];
    
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function app()
    {
        return $this->belongsTo('App\Models\App');
    }
}
