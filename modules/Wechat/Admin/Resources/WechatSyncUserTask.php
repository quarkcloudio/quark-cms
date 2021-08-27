<?php

namespace Modules\Wechat\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class WechatSyncUserTask extends Resource
{
    /**
     * 公众号类型
     *
     * @var string
     */
    public static $wechatType = 'DYH';

    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '同步任务';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'Modules\Wechat\Models\WechatSyncUserTask';

    /**
     * 表单接口
     *
     * @param  Request  $request
     * @return string
     */
    public function formApi($request)
    {
        return (new \Modules\Wechat\Admin\Actions\SyncUser)->api();
    }

    /**
     * 列表查询
     *
     * @param  Request  $request
     * @return object
     */
    public static function indexQuery(Request $request, $query)
    {
        return $query->orderBy('id', 'desc');
    }

    /**
     * 字段
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $taskInfo = $this->newModel()
        ->orderBy('id','desc')
        ->where('status', 1)
        ->first();

        $taskID = $taskInfo ? $taskInfo['id']+1 : 1;

        return [
            Field::hidden('id','ID')
            ->onlyOnForms(),

            Field::text('name','任务名称')
            ->rules(['required','max:100'],['required'=>'名称必须填写','max'=>'名称不能超过100个字符'])
            ->default('TaskID-'.$taskID),
    
            Field::radio('type','类型')
            ->options([
                'DYH' => '订阅号',
                'FWH' => '服务号',
                'MP' => '小程序',
            ])
            ->rules(['required'],['required'=>'请选择任务类型']),

            Field::text('total_num','任务总量')
            ->onlyOnIndex(),

            Field::text('num','已完成数量')
            ->onlyOnIndex(),

            Field::datetime('created_at','创建时间')
            ->onlyOnIndex(),

            Field::datetime('finished_at','完成时间')
            ->onlyOnIndex(),
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
            new \App\Admin\Searches\Input('name','名称'),
            new \App\Admin\Searches\DateTimeRange('created_at', '创建时间')
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
            (new \Modules\Wechat\Admin\Actions\SyncUser)->onlyOnIndex(),
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnTableRow(),
        ];
    }
}