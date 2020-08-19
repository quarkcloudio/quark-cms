<?php

namespace Modules\Mall\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsSku extends Model
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
        'shop_id',
        'properties',
        'property_ids',
        'property_names',
        'stock_num',
        'cost_price',
        'goods_price',
        'market_price',
        'goods_sn',
        'goods_barcode',
        'status'
    ];
}
