<?php

namespace Modules\Mall\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsCategory extends Model
{
    use SoftDeletes;
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
        'pid',
        'title',
        'sort',
        'cover_id',
        'goods_brand_ids',
        'name',
        'description',
        'index_tpl',
        'lists_tpl',
        'detail_tpl',
        'page_num',
        'status'
    ];
     
    protected $dates = ['delete_at'];
}
