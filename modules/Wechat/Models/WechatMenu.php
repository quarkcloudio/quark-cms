<?php

namespace Modules\Wechat\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class WechatMenu extends Model
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
        'id',
        'pid',
        'wechat_type',
        'name',
        'type',
        'value',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * 格式化字段
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 为数组 / JSON 序列化准备日期。
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
     * @param string $wechatType
     * 
     * @return object
     */
    public static function orderedList($wechatType)
    {
        $query = static::query();

        $lists = $query->where('status', 1)
        ->where('wechat_type', $wechatType)
        ->orderBy('id', 'asc')
        ->select('id', 'pid', 'name')
        ->get()
        ->toArray();

        $trees = list_to_tree($lists,'id','pid','children',0);
        $treeLists = tree_to_ordered_list($trees,0,'name','children');

        $list[0] = '根节点';
        foreach ($treeLists as $key => $treeList) {
            $list[$treeList['id']] = $treeList['name'];
        }

        return $list;
    }
}
