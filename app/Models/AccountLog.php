<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class AccountLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'adminid','uid', 'money','score', 'type','remark','status'
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
}