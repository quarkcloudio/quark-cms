<?php

namespace Modules\Wechat\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class WechatMenu extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '微信菜单';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'Modules\Wechat\Models\WechatMenu';

    /**
     * 列表查询
     *
     * @param  Request  $request
     * @return object
     */
    public static function indexQuery(Request $request, $query)
    {
        return $query->orderBy('id', 'asc');
    }

    /**
     * 字段
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Field::hidden('id','ID')
            ->onlyOnForms(),

            Field::text('name','名称')
            ->rules(['required','max:6'],['required'=>'名称必须填写','max'=>'名称不能超过6个字符']),
    
            Field::select('pid','父节点')
            ->options(\Modules\Wechat\Models\WechatMenu::orderedList())
            ->default(0),
    
            Field::select('type','类型')
            ->options([
                0 => '无',
                'view' => '链接',
                'click' => '点击事件',
                'media_id' => '图文消息',
            ])
            ->default(0),
    
            Field::text('value','值'),

            Field::switch('status','状态')
            ->trueValue('正常')
            ->falseValue('禁用')
            ->default(true)
            ->onlyOnForms(),
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
            new \App\Admin\Searches\Status,
            new \App\Admin\Searches\DateTimeRange('created_at', '发布时间')
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
            (new \Modules\Wechat\Admin\Actions\SyncMenu)->onlyOnIndex(),
            (new \Modules\Wechat\Admin\Actions\PublishMenu)->onlyOnIndex(),
            (new \App\Admin\Actions\CreateLink($this->title()))->onlyOnIndex(),
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnTableRow(),
            (new \App\Admin\Actions\EditLink('编辑'))->onlyOnTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnTableRow(),
        ];
    }

    /**
     * 列表页面显示前回调
     * 
     * @param Request $request
     * @param mixed $list
     * @return array
     */
    public function beforeIndexShowing(Request $request, $list)
    {
        // 转换成树形表格
        return !isset($request->search) ? list_to_tree($list,'id','pid','children', 0) : $list;
    }
}