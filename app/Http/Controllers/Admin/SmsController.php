<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sms;
use Illuminate\Http\Request;
use Quark;
use QuarkCMS\QuarkAdmin\Http\Controllers\Controller;

class SmsController extends Controller
{
    public $title = '短信';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $table = Quark::table(new Sms)->title($this->title);
        $table->column('id','ID');
        $table->column('phone','手机号');
        $table->column('code','验证码');
        $table->column('content','内容');
        $table->column('created_at','发送时间');
        $table->column('status','状态')->using(['1'=>'发送成功','0'=>'发送失败']);

        $table->column('actions','操作')->width(120)->actions(function($action,$row) {
            $action->a('发送')
            ->api(backend_url('api/admin/sms/sendSms?id='.$row['id']))
            ->withConfirm('确认要发送短信吗？','确认后将重新发送短信！');
            
            $action->a('删除')
            ->withPopconfirm('确认要删除吗？')
            ->model()
            ->where('id','{id}')
            ->delete();
        });

        // 头部操作
        $table->toolBar()->actions(function($action) {
            $action->button('发送短信')
            ->type('primary')
            ->link(frontend_url('/sms/send',false));
        });

        $table->batchActions(function($action) {
            $action->a('批量删除')
            ->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！')
            ->model()
            ->whereIn('id','{ids}')
            ->delete();

            $action->a('批量发送')
            ->withConfirm('确认要群发短信吗？','确认后将重新发送短信！')
            ->api('admin/sms/sendSms?id={ids}');
        });

        $table->search(function($search) {
            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'发送成功',0=>'发送失败'])
            ->placeholder('选择状态');

            $search->where('phone', '搜索内容',function ($query) {
                $query->where('phone', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->between('created_at', '发送时间')->datetime();
        });

        $table->model()->orderBy('id', 'desc')->paginate(request('pageSize',10));

        return $table;
    }

    // 上传文件
    public function import(Request $request)
    {
        $fileId = $request->input('fileId');

        $results = import($fileId);

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
     *
     * @param  Request  $request
     * @return Response
     */
    public function sendSms(Request $request)
    {
        $id = $request->input('id');

        if(empty($id)) {
            return error('参数错误！');
        }

        // 定义对象
        $query = Sms::query();

        if(is_array($id)) {
            $query->whereIn('id',$id);
        } else {
            $query->where('id',$id);
        }

        $sms = $query->get();

        $sendResult = true;

        foreach ($sms as $key => $value) {
            $result = sioo_send_sms($value['phone'],$value['content']);
            if($result['status'] == 'success') {
                $data['status'] = 1;
                Sms::where('id',$value['id'])->update($data);
            } else {
                $sendResult =false;
                $data['status'] = 0;
                Sms::where('id',$value['id'])->update($data);
            }
        }

        if ($sendResult) {
            return success('短信发送成功！');
        } else {
            return error('短信发送失败，'.$result['msg']);
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

            foreach ($phones as $key => $phone) {
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
