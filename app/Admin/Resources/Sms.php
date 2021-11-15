<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class Sms extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '短信';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Sms';

    /**
     * 分页
     *
     * @var int|bool
     */
    public static $perPage = 10;

    /**
     * 字段
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Field::text('phone','手机号')->onlyOnIndex(),
            Field::text('code','验证码')->onlyOnIndex(),
            Field::text('content','内容')->onlyOnIndex(),
            Field::datetime('created_at','发送时间')->onlyOnIndex(),
            Field::radio('status','状态')->options([
                0=>'发送失败',
                1=>'发送成功'
            ])
        ];
    }

    /**
     * 搜索表单
     *
     * @param  Request  $request
     * @return object
     */
    public function searches(Request $request)
    {
        return [
            new \App\Admin\Searches\Input('phone', '手机号'),
            new \App\Admin\Searches\Status,
            new \App\Admin\Searches\DateTimeRange('created_at', '发送时间')
        ];
    }

    /**
     * 行为
     *
     * @param  Request  $request
     * @return object
     */
    public function actions(Request $request)
    {
        return [
            (new \App\Admin\Actions\ImportSmsLink())->onlyOnIndex(),
            (new \App\Admin\Actions\SendSms('批量发送'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\SendSms('发送'))->onlyOnIndexTableRow(),
        ];
    }
}