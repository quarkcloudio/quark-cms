<?php

namespace App\Admin\Controllers;

use App\Models\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    /**
     * 导入手机号
     * @param  integer
     * @return string
     */
    public function import(Request $request)
    {
        $results = import($request->input('fileId'));

        foreach ($results as $key => $value) {
            $phones[] = $value[0];
        }

        $phones = implode("\r",$phones);

        if($phones) {
            return success('导入成功！','',$phones);
        } else {
            return error('导入失败！');
        }
    }

    /**
     * 发送短信验证码
     * @param  integer
     * @return string
     */
    public function sendImportSms(Request $request)
    {
        $phones = $request->json('phone');
        $content = $request->json('content');
        $phones = explode("\r", $phones);
        $sendResult = true;

        if(is_array($phones)) {
            $phones = array_values(array_unique($phones));

            foreach ($phones as $phone) {
                if(empty($phone)) {
                    return error('手机号不能为空！');
                }

                if(!preg_match("/^1[34578]\d{9}$/", $phone)) {
                    return error('手机号格式不正确！');
                }

                if(empty($content)) {
                    return error('内容不能为空！');
                }

                $sendDayCount = Sms::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
                    ->where('phone',$phone)->count();

                // 每天最多发送6条短信
                if($sendDayCount >6) {
                    return error('抱歉，每个手机号一天最多获取6条短信！');
                }

                $result = sioo_send_sms($phone,$content);

                $data['phone'] = $phone;
                $data['content'] = $content;

                if($result['status'] == 'success') {
                    $data['status'] = 1;
                    Sms::create($data);
                } else {
                    $sendResult =false;

                    $data['status'] = 0;
                    Sms::create($data);
                }
            }
        } else {
            if(empty($phone)) {
                return error('手机号不能为空！');
            }

            if(!preg_match("/^1[34578]\d{9}$/", $phone)) {
                return error('手机号格式不正确！');
            }

            if(empty($content)) {
                return error('内容不能为空！');
            }

            $sendDayCount = Sms::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
                ->where('phone',$phone)->count();

            // 每天最多发送6条短信
            if($sendDayCount >6) {
                return error('抱歉，每个手机号一天最多获取6条短信！');
            }

            $result = sioo_send_sms($phone,$content);

            $data['phone'] = $phone;
            $data['content'] = $content;

            if($result['status'] == 'success') {
                $data['status'] = 1;
                Sms::create($data);
            } else {
                $sendResult =false;
                $data['status'] = 0;
                Sms::create($data);
            }
        }

        if($sendResult) {
            return success('短信已发送！');
        } else {
            return error('短信发送失败，'.$result['msg']);
        }
    }
}
