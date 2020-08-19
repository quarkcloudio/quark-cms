<?php

namespace Modules\Mall\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable=[
        'uid',
        'score',
        'money',
        'bank_name',
        'bank_payee',
        'bank_number',
        'last_login_ip',
        'last_login_time',
        'status'
    ];
    
    protected $dates = ['delete_at'];
}