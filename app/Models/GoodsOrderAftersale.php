<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsOrderAftersale extends Model
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
        'goods_order_detail_id',
        'goods_id',
        'refund_amount',
        'refund_consignee_name',
        'refund_consignee_phone',
        'refund_consignee_province',
        'refund_consignee_city',
        'refund_consignee_county',
        'refund_consignee_town',
        'refund_consignee_address',
        'aftersale_reason',
        'cover_ids',
        'aftersale_type',
        'status'
    ];

    public function goods()
    {
        return $this->hasOne('App\Models\Goods', 'id', 'goods_id');
    }

    public function goodsOrderDetail()
    {
        return $this->hasOne('App\Models\GoodsOrderDetail', 'id', 'goods_order_detail_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'uid');
    }

    public function shop()
    {
        return $this->hasOne('App\Models\Shop', 'id', 'shop_id');
    }
}
