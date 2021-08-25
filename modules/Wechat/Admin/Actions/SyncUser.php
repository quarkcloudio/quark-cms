<?php

namespace  Modules\Wechat\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Action;
use EasyWeChat\Factory;
use App\User;

class SyncUser extends Action
{
    /**
     * 行为名称
     *
     * @var string
     */
    public $name = '同步微信用户';

    /**
     * 执行行为
     *
     * @param  Fields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function handle($fields, $models)
    {
        $nextOpenid = $fields->input('nextOpenid',null);
        $type = $fields->input('type','dyh');

        $app = Factory::officialAccount(wechat_config($type));

        $users = $app->user->list($nextOpenid);

        if($users['data']['openid']) {
            foreach ($users['data']['openid'] as $key => $value) {
                $user = $app->user->get($value);
                $wechatUserInfo = User::where('wechat_openid',$user['openid'])->first();

                if(empty($wechatUserInfo)) {
                    $data['wechat_openid'] = $user['openid'];
                    $data['wechat_unionid'] = $user['unionid'];
                    $data['type'] = $type;
                    User::create($data);
                } else {
                    $data['wechat_openid'] = $user['openid'];
                    $data['wechat_unionid'] = $user['unionid'];
                    $data['type'] = $type;
                    User::where('wechat_openid',$user['openid'])->update($data);
                }
            }
        }
    }
}