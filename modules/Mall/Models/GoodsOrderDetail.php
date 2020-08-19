<?php

namespace Modules\Mall\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsOrderDetail extends Model
{
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;
    
    /**
     * 增加自定义属性
     *
     * @var bool
     */
    protected $appends = [
        'total_amount',
    ];

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
        'uid',
        'shop_id',
        'order_id',
        'goods_order_id',
        'goods_id',
        'goods_sku_id',
        'goods_name',
        'num',
        'goods_properties',
        'goods_property_names',
        'goods_price',
        'market_price',
        'description',
        'cover_id',
        'status'
    ];

    /**
     * 获取订单详情支付金额
     *
     * @var array
     */
    public function getTotalAmountAttribute()
    {
        return number_format($this->goods_price*$this->num,2);
    }
}
