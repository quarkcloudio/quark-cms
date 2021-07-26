<?php

namespace App\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Action;
use App\Models\Sms;

class SendSms extends Action
{
    /**
     * 行为名称
     *
     * @var string
     */
    public $name = null;

    /**
     * 设置按钮类型,primary | ghost | dashed | link | text | default
     *
     * @var string
     */
    public $type = 'link';

    /**
     * 设置按钮大小,large | middle | small | default
     *
     * @var string
     */
    public $size = 'small';

    /**
     * 初始化
     *
     * @param  string  $name
     * 
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->withConfirm('确认要发送短信吗？','确认后将重新发送短信！');
    }

    /**
     * 行为接口接收的参数，当行为在表格行展示的时候，可以配置当前行的任意字段
     *
     * @return array
     */
    public function apiParams()
    {
        return ['id'];
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
        $sms = $model->get();
        $sendResult = true;

        foreach ($sms as $value) {
            $result = sioo_send_sms($value['phone'],$value['content']);

            if($result['status'] == 'success') {
                Sms::where('id',$value['id'])->update(['status' => 1]);
            } else {
                $sendResult =false;
                Sms::where('id',$value['id'])->update(['status' => 0]);
            }
        }

        return $sendResult ? success('短信发送成功！') : error('短信发送失败，'.$result['msg']);
    }
}