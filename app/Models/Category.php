<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class Category extends Model
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
     * @param  string $type
     * @return object
     */
    public static function orderedList($type)
    {
        $lists = static::query()
        ->where('status', 1)
        ->where('type', $type)
        ->orderBy('sort', 'asc')
        ->orderBy('id', 'asc')
        ->select('id', 'pid', 'title')
        ->get()
        ->toArray();

        $trees = list_to_tree($lists,'id','pid','children',0);
        $treeLists = tree_to_ordered_list($trees,0,'title','children');

        $list[0] = '根节点';
        foreach ($treeLists as $key => $treeList) {
            $list[$treeList['id']] = $treeList['title'];
        }

        return $list;
    }
}