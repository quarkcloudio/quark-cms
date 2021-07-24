<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class Post extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'adminid',
        'uid',
        'category_id',
        'tags',
        'uuid',
        'title',
        'name',
        'author',
        'source',
        'description',
        'password',
        'cover_ids',
        'pid',
        'level',
        'type',
        'show_type',
        'position',
        'content',
        'comment',
        'view',
        'page_tpl',
        'file_ids',
        'comment_status',
        'status'
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

    /**
     * 分类
     *
     * @param  void
     * @return object
     */
    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    /**
     * 获取单页的有序列表
     *
     * @return object
     */
    public static function orderedList()
    {
        $lists = static::query()
        ->where('status', 1)
        ->where('type', 'PAGE')
        ->orderBy('level', 'desc')
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