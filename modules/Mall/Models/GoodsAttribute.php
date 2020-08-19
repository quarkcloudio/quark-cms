<?php

namespace Modules\Mall\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsAttribute extends Model
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
        'shop_id',
        'goods_type_id',
        'name',
        'description',
        'style',
        'sort',
        'status',
        'type'
    ];

    public function goodsType()
    {
        return $this->hasOne('App\Models\GoodsType', 'id', 'goods_type_id');
    }
}
