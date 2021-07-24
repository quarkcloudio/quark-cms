<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Comment extends Model
{    
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 属性黑名单
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    /**
     * 文章
     *
     * @return object
     */
    public function article()
    {
        return $this->hasOne('App\Models\Post', 'id', 'object_id');
    }

    /**
     * 商品
     *
     * @return object
     */
    public function goods()
    {
        return $this->hasOne('App\Models\Goods', 'id', 'object_id');
    }

    /**
     * 用户
     *
     * @return object
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'uid');
    }
}