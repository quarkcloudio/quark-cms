<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsBrand extends Model
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
        'name',
        'letter',
        'site_url',
        'logo',
        'promotion_image',
        'description',
        'is_recommend',
        'sort',
        'status'
    ];
}
