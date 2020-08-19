<?php

namespace Modules\Mall\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsInfoAttributeValue extends Model
{
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'goods_id',
        'goods_sku_id',
        'goods_attribute_id',
        'goods_attribute_value_id',
        'type'
    ];
}
