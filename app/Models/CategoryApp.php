<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryApp extends Model
{
    //

    public function app()
    {
        return $this->hasOne('App\Models\App');
    }
}
