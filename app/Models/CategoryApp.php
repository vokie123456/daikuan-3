<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryApp extends Model
{
    //

    public function app()
    {
        return $this->belongsTo('App\Models\App', 'app_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\Category');
    }
}
