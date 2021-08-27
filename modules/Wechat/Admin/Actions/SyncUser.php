<?php

namespace  Modules\Wechat\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Link;
use Modules\Wechat\Jobs\SyncUserJob;
use EasyWeChat\Factory;
use App\User;
use Modules\Wechat\Models\WechatSyncUserTask;
use Illuminate\Support\Str;

class SyncUser extends Link
{
    /**
     * 行为名称，当行为在表格行展示时，支持js表达式
     *
     * @var string
     */
    public $name = '创建任务';

    /**
     * 设置按钮类型,primary | ghost | dashed | link | text | default
     *
     * @var string
     */
    public $type = 'primary';

    /**
     * 设置图标
     *
     * @var string
     */
    public $icon = 'plus-circle';

    /**
     * 任务id
     *
     * @var int
     */
    public $taskId = null;

    /**
     * 跳转链接
     *
     * @return string
     */
    public function href()
    {
        return '#/index?api=' . Str::replaceLast('/index', '/create', 
            Str::replaceFirst('api/','',\request()->path())
        );
    }

    /**
     * 执行行为
     *
     * @param  Fields  $fields
     * @param  Collection  $model
     * @return mixed
     */
    public function handle($fields, $model)
    {
        set_time_limit(0);
        ini_set('memory_limit','1024M');

        $hasTasking = WechatSyncUserTask::where('finished_at',NULL)
        ->where('type',$fields->type)
        ->first();

        if($hasTasking) {
            return error('请等队列中正在执行的任务完成！');
        }

        $getLastUser = User::orderBy('id','desc')->first();

        switch (strtolower($fields->type)) {
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

        if(wechat_config($fields->type) === false) {
            return error('请先配置公众号！');
        }

        $app = Factory::officialAccount(wechat_config($fields->type));
        $users = $app->user->list($nextOpenid);

        if($users['count']<=0) {
            return error('您的用户数据已经是最新的了！');
        }

        $data['name'] = $fields->name;
        $data['type'] = $fields->type;
        $data['start_openid'] = $nextOpenid;
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->taskId = $model->insertGetId($data);

        $result = $this->addToQueue($fields->type, $nextOpenid);

        if($result) {
            return success('创建成功！','/#/index?api=admin/wechatSyncUserTask/index');
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

        if(isset($users['data']['openid'])) {
            WechatSyncUserTask::where('id',$this->taskId)->increment('total_num', $users['count']);

            foreach ($users['data']['openid'] as $key => $value) {

                $payload['wechat_type'] = $type;
                $payload['openid'] = $value;
                $payload['task_id'] = $this->taskId;
                SyncUserJob::dispatch($payload)->onConnection('redis');
            }
        } else {
            return true;
        }

        if($users['next_openid']) {
            return $this->addToQueue($type, $users['next_openid']);
        } else {
            return true;
        }
    }
}