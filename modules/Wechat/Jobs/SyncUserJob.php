<?php

namespace Modules\Wechat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;
use EasyWeChat\Factory;
use App\User;

class SyncUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 队列载荷
     *
     * @var any
     */
    public $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->payload;
        $app = Factory::officialAccount(wechat_config($payload['wechat_type']));
        $user = $app->user->get($payload['openid']);

        $query = User::query();

        switch (strtolower($payload['wechat_type'])) {
            case 'dyh':
                $query = $query->where('wechat_dyh_openid',$user['openid']);

                $data['wechat_dyh_openid'] = $user['openid'];
                break;

            case 'fwh':
                $query = $query->where('wechat_fwh_openid',$user['openid']);
                $data['wechat_fwh_openid'] = $user['openid'];
                break;

            case 'mp':
                $query = $query->where('wechat_mp_openid',$user['openid']);
                $data['wechat_mp_openid'] = $user['openid'];
                break;

            default:
                die();
                break;
        }

        if(isset($user['unionid'])) {
            $query = $query->orWhere('wechat_unionid', $user['unionid']);
        }

        $hasUser = $query->first();

        $data['wechat_unionid'] = $user['unionid'];

        if(empty($hasUser)) {

            // 将微信头像保存到服务器
            $avatarInfo = download_picture_to_storage($user['headimgurl']);

            if($avatarInfo['status'] == 'error') {
                return $avatarInfo;
            }

            $data['username'] = Str::random(8) . '-' . time(); // 临时用户名
            $data['email'] = Str::random(8) . '-' . time(); // 临时邮箱
            $data['phone'] = Str::random(8) . '-' . time(); // 临时手机号
            $data['nickname'] = $user['nickname'];
            $data['sex'] = $user['sex'];
            $data['password'] = bcrypt(env('APP_KEY'));
            $data['avatar'] = json_encode($avatarInfo['data']);
            $data['last_login_ip'] = request()->ip();
            $data['last_login_time'] = date('Y-m-d H:i:s');

            // 插入用户表
            $uid = User::insertGetId($data);
    
            $updateData['phone'] = Str::random(8) . '-' . $uid;
            $updateData['email'] = Str::random(8) . '-' . $uid;
            $updateData['username'] = Str::random(8) . '-' . $uid;

            // 更新用户表
            User::where('id',$uid)->update($updateData);
        } else {
            User::where('id',$hasUser['id'])->update($data);
        }
    }
}
