<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
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