<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class Navigation extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '导航';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Navigation';

    /**
     * 列表查询
     *
     * @param  Request  $request
     * @return object
     */
    public static function indexQuery(Request $request, $query)
    {
        return $query->orderBy('sort', 'asc');
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

            Field::text('title','标题')
            ->rules(
                ['required'],
                ['required' => '标题必须填写']
            ),

            Field::text('sort','排序')
            ->default(0)
            ->editable(),

            Field::select('pid','父节点')
            ->options(\App\Models\Navigation::orderedList())
            ->default(0)
            ->onlyOnForms(),

            Field::text('url','链接')
            ->editable(),

            Field::image('cover_id','封面图', function() {
                $coverId = \json_decode($this->cover_id,true);

                return is_array($coverId) ? get_picture($coverId['id']) : null;
            }),

            Field::switch('status','状态')
            ->editable()
            ->trueValue('正常')
            ->falseValue('禁用')
            ->default(true)
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
            new \App\Admin\Searches\Input('title', '标题'),
            new \App\Admin\Searches\Status
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
            (new \App\Admin\Actions\CreateDrawer($this->title()))->onlyOnIndex(),
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnTableRow(),
            (new \App\Admin\Actions\EditDrawer('编辑'))->onlyOnTableRow(),
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