<?php

namespace Modules\Wechat\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Action;
use EasyWeChat\Factory;

class PublishMenu extends Action
{
    /**
     * 行为名称
     *
     * @var string
     */
    public $name = '发布菜单';

    /**
     * 执行行为
     *
     * @param  Fields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function handle($fields, $models)
    {
        $type = $fields->input('type','dyh');

        $app = Factory::officialAccount(wechat_config($type));

        // 查询列表
        $menus = $models->orderBy('id', 'asc')
        ->get()
        ->toArray();

        $menuTree = list_to_tree($menus,'id','pid','sub_button',0);

        $getMenuTree = [];

        foreach ($menuTree as $key => $value) {

            $children = null;
            if(isset($value['sub_button'])) {
                foreach ($value['sub_button'] as $subKey => $subValue) {
                    $children[] = $this->filterValue($subValue);
                }
            }

            $getValue = $this->filterValue($value);

            if($children) {
                $getValue['sub_button'] = $children;
            }

            $getMenuTree[] = $getValue;
        }

        // 发布菜单
        $result = $app->menu->create($getMenuTree);

        if($result['errcode'] != 0) {
            return error($result['errmsg']);
        } else {
            return success('操作成功！');
        }
    }

    /**
    * 过滤菜单值
    *
    * @param  Request  $request
    * @return Response
    */
    protected function filterValue($value)
    {
        unset($value['id']);
        unset($value['pid']);
        unset($value['status']);
        unset($value['created_at']);
        unset($value['updated_at']);

        switch ($value['type']) {
            case 'view':

                $value['url'] = $value['value'];
                break;
            
            default:

                unset($value['type']);
                break;
        }

        unset($value['value']);

        return $value;
    }
}