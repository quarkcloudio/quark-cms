<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
     public $timestamps = false;
     
    /**
     * 属性黑名单
     *
     * @var array
     */
    protected $guarded = [];
}