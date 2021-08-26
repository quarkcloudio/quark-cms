<?php

namespace  Modules\Wechat\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Action;
use Modules\Wechat\Jobs\SyncUserJob;
use EasyWeChat\Factory;
use App\User;

class SyncUser extends Action
{
    /**
     * 公众号类型
     *
     * @var string
     */
    public $wechatType = 'DYH';

    /**
     * 初始化
     *
     * @param  string  $name
     * @param  string  $wechatType
     * 
     * @return void
     */
    public function __construct($name, $wechatType)
    {
        $this->name = $name;
        $this->wechatType = $wechatType;
    }

    /**
     * 执行行为
     *
     * @param  Fields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function handle($fields, $models)
    {
        set_time_limit(0);
        ini_set('memory_limit','1024M');

        $getLastUser = User::orderBy('id','desc')->first();

        switch (strtolower($this->wechatType)) {
            case 'dyh':
                $nextOpenid = $getLastUser['wechat_dyh_openid'];
                break;

            case 'fwh':
                $nextOpenid = $getLastUser['wechat_fwh_openid'];
                break;

            case 'mp':
                $nextOpenid = $getLastUser['wechat_mp_openid'];
                break;

            default:
                die();
                break;
        }

        $result = $this->addToQueue($this->wechatType, $nextOpenid);

        if($result) {
            return success('任务创建成功！');
        } else {
            return error('操作失败！');
        }
    }

    /**
     * 加入队列
     *
     * @param  Fields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function addToQueue($type, $nextOpenid = null)
    {
        $app = Factory::officialAccount(wechat_config($type));
        $users = $app->user->list($nextOpenid);

        if($users['data']['openid']) {
            foreach ($users['data']['openid'] as $key => $value) {
                $payload['wechat_type'] = $type;
                $payload['openid'] = $value;
                SyncUserJob::dispatch($payload)->onConnection('redis');
            }
        }

        if($users['next_openid']) {
            return $this->addToQueue($type, $users['next_openid']);
        } else {
            return true;
        }
    }
}