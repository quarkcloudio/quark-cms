<?php

namespace  Modules\Wechat\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Action;
use EasyWeChat\Factory;

class SyncMenu extends Action
{
    /**
     * 行为名称
     *
     * @var string
     */
    public $name = '同步菜单';

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
        
        $menus = $app->menu->current();

        if(!empty($menus)) {
            $models->where('status',1)->delete();
        }

        foreach ($menus['selfmenu_info']['button'] as $key => $value) {

            $data['name'] = $value['name'];
            $data['type'] = isset($value['type']) ? $value['type'] : null;
            $data['value'] = isset($value['url']) ? $value['url'] : null;

            $result = $models->create($data);

            if(isset($value['sub_button']['list'])) {
                foreach ($value['sub_button']['list'] as $subKey => $subValue) {

                    $subData['pid'] = $result->id;
                    $subData['name'] = $subValue['name'];
                    $subData['type'] = isset($subValue['type']) ? $subValue['type'] : null;
                    $subData['value'] = isset($subValue['url']) ? $subValue['url'] : null;

                    if($subValue['type'] !== 'news') {
                        $models->create($subData);
                    }
                }
            }
        }

        if($result) {
            return success('同步成功！');
        } else {
            return error('同步失败，请重试！');
        }
    }
}