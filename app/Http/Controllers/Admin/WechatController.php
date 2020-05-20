<?php

namespace App\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use App\Models\WechatUser;
use QuarkCMS\QuarkAdmin\Models\Config;
use App\Models\File;
use App\User;
use Quark;

class WechatController extends QuarkController
{
    public $title = '配置';

    /**
     * Form页面模板
     * 
     * @param  Request  $request
     * @return Response
     */
    protected function configForm()
    {
        $groupNames = Config::where('status', 1)
        ->whereIn('group_name',['微信订阅号','微信服务号','微信小程序'])
        ->distinct()
        ->pluck('group_name');

        $form = Quark::form()->setAction('admin/wechat/saveConfig');

        foreach ($groupNames as $key => $groupName) {
            if($groupName) {
                $configs = Config::where('status', 1)
                ->where('group_name',$groupName)
                ->get()
                ->toArray();
    
                $form->tab($groupName, function ($form) use ($configs) {
                    foreach ($configs as $key => $config) {
                        switch ($config['type']) {
                            case 'text':
                                $form->text($config['name'],$config['title'])
                                ->extra($config['remark'])
                                ->value($config['value']);
                                break;
                            case 'file':
                                $files = null;
                                if($config['value']) {
                                    $file['id'] = $config['value'];
                                    $file['uid'] = $config['value'];
                                    $file['name'] = get_file($config['value'],'name');
                                    $file['size'] = get_file($config['value'],'size');
                                    $file['url'] = get_file($config['value'],'path');
                                    $files[] = $file;
                                }

                                $form->file($config['name'],$config['title'])
                                ->extra($config['remark'])
                                ->button('上传'.$config['title'])
                                ->value($files);

                                break;
                            case 'textarea':
                                $form->textArea($config['name'],$config['title'])
                                ->extra($config['remark'])
                                ->width(400)
                                ->value($config['value']);
                                break;
                            case 'switch':
                                $form->switch($config['name'],$config['title'])
                                ->extra($config['remark'])
                                ->options([
                                    'on'  => '开启',
                                    'off' => '关闭'
                                ])->value($config['value']);

                                break;
                            case 'picture':

                                $image = null;
                                if($config['value']) {
                                    $image['id'] = $config['value'];
                                    $image['name'] = get_picture($config['value'],0,'name');
                                    $image['size'] = get_picture($config['value'],0,'size');
                                    $image['url'] = get_picture($config['value'],0,'path');
                                }

                                $form->image($config['name'],$config['title'])
                                ->extra($config['remark'])
                                ->button('上传'.$config['name'])
                                ->value($image);

                                break;
                            default:
                                $form->text($config['name'],$config['title'])
                                ->extra($config['remark'])
                                ->value($config['value']);
                                break;
                        }
                    }
                });
            }
        }

        return $form;
    }

    /**
     * 管理页面
     *
     * @param  Request  $request
     * @return Response
     */
    public function config(Request $request)
    {
        $form = $this->configForm();

        $content = Quark::content()
        ->title($this->title())
        ->body(['form'=>$form->render()]);

        return success('获取成功！','',$content);
    }

    /**
    * 保存站点配置数据
    *
    * @param  Request  $request
    * @return Response
    */
    public function saveConfig(Request $request)
    {
        $requestJson    =   $request->getContent();
        $requestData    =   json_decode($requestJson,true);

        $result = true;

        // 遍历插入数据
        foreach ($requestData as $key => $value) {

            // 修改时清空缓存
            Cache::pull($key);

            $getResult = Config::where('name',$key)->update(['value'=>$value]);
            if($getResult === false) {
                $result = false;
            }
        }

        if ($result) {
            return success('操作成功！','');
        } else {
            return error('操作失败！');
        }
    }

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
}
