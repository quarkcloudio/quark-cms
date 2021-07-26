<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Modules\Wechat\Models\WechatUser;

class WechatController extends Controller
{
    // 菜单按钮事件
    public function menuIndex(Request $request)
    {
        $type = $request->input('type','dyh');

        $app = Factory::officialAccount(wechat_config($type));
        $menus = $app->menu->current();

        if (isset($menus['selfmenu_info'])) {
            return success('获取成功！','',$menus);
        } else {
            return error('获取失败！');
        }
    }

    // 菜单按钮事件
    public function menuCreate(Request $request)
    {
        $type = $request->input('type','dyh');
        $data = $request->input('data');

        $app = Factory::officialAccount(wechat_config($type));
        $getButtons = json_decode($data,true);

        $buttonTree = list_to_tree($getButtons,'id','pId',$child = 'sub_button');

        // 创建菜单
        $result = $app->menu->create($buttonTree);

        if($result['errcode'] != 0) {
            return $this->error($result['errmsg']);
        } else {
            return $this->success('操作成功！');
        }
    }

    // todo同步用户
    public function syncUsers(Request $request)
    {
        $nextOpenid = $request->input('nextOpenid',null);
        $type = $request->input('type','dyh');

        $app = Factory::officialAccount(wechat_config($type));

        $users = $app->user->list($nextOpenid);

        if($users['data']['openid']) {
            foreach ($users['data']['openid'] as $key => $value) {
                $user = $app->user->get($value);
                $wechatUserInfo = WechatUser::where('wechat_openid',$user['openid'])->first();

                if(empty($wechatUserInfo)) {
                    $data['wechat_openid'] = $user['openid'];
                    $data['wechat_unionid'] = $user['unionid'];
                    $data['type'] = $type;
                    WechatUser::create($data);
                } else {
                    $data['wechat_openid'] = $user['openid'];
                    $data['wechat_unionid'] = $user['unionid'];
                    $data['type'] = $type;
                    WechatUser::where('wechat_openid',$user['openid'])->update($data);
                }

                if($key == 500) {
                    echo ("进行下一步同步，正在跳转...<script>window.location.href='".url('index/syncUsers?nextOpenid='.$user['openid'])."';</script>");
                    break;
                }
            }
        }
    }
}
