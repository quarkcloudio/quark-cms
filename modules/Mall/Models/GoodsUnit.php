<?php

namespace Modules\Mall\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsUnit extends Model
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
        'id',
        'shop_id',
        'name',
        'status'
    ];
}
