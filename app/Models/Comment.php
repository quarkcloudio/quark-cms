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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pid', 
        'uid',
        'object_id',
        'title',
        'cover_ids',
        'type',
        'content',
        'ding',
        'cai',
        'report',
        'status',
        'rate'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

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

    public function article()
    {
        return $this->hasOne('App\Models\Post', 'id', 'object_id');
    }

    public function goods()
    {
        return $this->hasOne('App\Models\Goods', 'id', 'object_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'uid');
    }
}