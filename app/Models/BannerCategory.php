<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class BannerCategory extends Model
{
    use SoftDeletes;

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
     * 获取菜单的有序列表
     *
     * @param  void
     * @return object
     */
    public static function list()
    {
        $lists = static::query()->where('status', 1)
        ->select('id','title')
        ->get()
        ->toArray();

        $result[0] = '根节点';
        foreach ($lists as $list) {
            $result[$list['id']] = $list['title'];
        }

        return $result;
    }
}