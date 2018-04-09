<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    //白名单
    // protected $fillable = [
    //     'id',
    //     'name',
    //     'weburl',
    //     'icon',
    //     'company_id',
    //     'synopsis',
    //     'details',
    //     'rate',
    //     'rate_type',
    //     'moneys',
    //     'terms',
    //     'repayments',
    //     'apply_number',
    //     'recommend',
    //     'status',
    // ];

    //黑名单
    protected $guarded = [];

    //
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }
}
