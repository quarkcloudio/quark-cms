<?php

namespace App\Modules\Wechat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Helper;
use EasyWeChat\Factory;
use App\User;
use App\Models\WechatUser;
use DB;
use Session;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
{
    /**
     * token
     *
     * @return \Illuminate\Http\Response
     */
    public function token(Request $request)
    {

        $type = $request->get('type');

        switch ($type) {
            case 'dyh':
                // 这里使用的是订阅号
                $app = Factory::officialAccount(wechat_config('dyh'));
                break;
            case 'fwh':
                // 这里使用的是服务号
                $app = Factory::officialAccount(wechat_config('fwh'));
                break;
            default:
                $app = Factory::officialAccount(wechat_config('fwh'));
                break;
        }

        // 从项目实例中得到服务端应用实例。
        $app->server->push(function ($message) use($app,$type) {

            $openid = $message['FromUserName']; // 用户的 openid

            $user = $app->user->get($openid);

            switch ($message['MsgType']) {
                case 'event':
                    switch ($message['Event']) {
                        case 'subscribe':

                            // 判断是否存在
                            $hasWechatUser = WechatUser::where('wechat_openid',$user['openid'])
                            ->where('wechat_unionid',$user['unionid'])
                            ->first();
                            
                            if(empty($hasWechatUser)) {
                                $data['wechat_openid'] = $user['openid'];
                                $data['wechat_unionid'] = $user['unionid'];

                                if($type == 'dyh') {
                                    $data['type'] = 'DYH';
                                } else {
                                    $data['type'] = 'FWH';
                                }

                                $result = WechatUser::create($data);
                            }

                            return "欢迎关注微信公众平台！";
                            break;
                        case 'CLICK':
                            return $this->menuClick($message);
                            break;
                        default:
                            # code...
                            break;
                    }
                    break;
                case 'text':
                    $content = $message['Content'];
                    if($content == 'test') {
                        return "ok";
                    }

                    break;
                default:
                    return "欢迎关注微信公众平台！";
                    break;
            }
        });

        $response = $app->server->serve();

        $response->send();

        return  $response;
    }

    // 菜单按钮事件
    public function menuClick($message)
    {
        $eventKey = $message['EventKey'];
        $openid = $message['FromUserName']; // 用户的 openid
        switch ($eventKey) {
            case 'test':
                return "敬请期待！";
                break;
            default:
                # code...
                break;
        }
    }
}
