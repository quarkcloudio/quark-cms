<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;
use QuarkCMS\Quark\Facades\TabPane;
use App\Models\Category;

class ArticleCategory extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '文章分类';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Category';

    /**
     * 列表查询
     *
     * @param  Request  $request
     * @return object
     */
    public static function indexQuery(Request $request, $query)
    {
        return $query->orderBy('sort', 'asc')->where('type','ARTICLE');
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
            (TabPane::make('基本', $this->baseFields())),
            (TabPane::make('扩展', $this->extendFields()))
        ];
    }

    /**
     * 基础标签页字段
     *
     * @return array
     */
    public function baseFields()
    {
        return [
            Field::hidden('id','ID')
            ->onlyOnForms(),

            Field::text('title','标题')
            ->rules(
                ['required','max:200'],
                ['required' => '标题必须填写', 'max' => '标题不能超过200个字符']
            ),

            Field::text('name','缩略名')
            ->editable()
            ->rules(
                ['max:200'],
                ['max'=>'缩略名不能超过200个字符']
            ),

            Field::select('pid','父节点')
            ->options(Category::orderedList('ARTICLE')),

            Field::textArea('description','描述')
            ->rules(['max:200'],['max'=>'名称不能超过200个字符'])
            ->onlyOnForms(),

            Field::number('sort','排序')
            ->editable(),

            Field::switch('status','状态')
            ->editable()
            ->trueValue('正常')
            ->falseValue('禁用')
            ->onlyOnForms(),
        ];
    }

    /**
     * 扩展标签页字段
     *
     * @return array
     */
    public function extendFields()
    {
        return [
            Field::image('cover_id','封面图')
            ->mode('single')
            ->onlyOnForms(),

            Field::text('index_tpl','频道模板')
            ->onlyOnForms(),

            Field::text('lists_tpl','列表模板')
            ->onlyOnForms(),

            Field::text('detail_tpl','详情模板')
            ->onlyOnForms(),

            Field::number('page_num','分页数量')
            ->editable(),

            Field::switch('status','状态')
            ->editable()
            ->trueValue('正常')
            ->falseValue('禁用')
            ->onlyOnIndex()
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
            new \App\Admin\Searches\Category,
            new \App\Admin\Searches\Status,
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

    /**
     * 表单显示前回调
     * 
     * @param Request $request
     * @return array
     */
    public function beforeCreating(Request $request)
    {
        return [
            'sort' => 0,
            'page_num' => 10,
            'status' => true
        ];
    }
}