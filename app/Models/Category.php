<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table = 'category';

    //黑名单
    protected $guarded = [];

    //不用自动添加created_at和updated_at
    public $timestamps = false;
}
