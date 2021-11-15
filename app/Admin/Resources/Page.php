<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;
use App\Models\Post;

class Page extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '单页';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Post';

    /**
     * 列表查询
     *
     * @param  Request  $request
     * @return object
     */
    public static function indexQuery(Request $request, $query)
    {
        return $query->orderBy('id','asc')->where('type','PAGE');
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

            Field::hidden('adminid','ADMINID')
            ->onlyOnForms(),

            Field::hidden('type','类型')
            ->onlyOnForms(),

            Field::text('title','标题')
            ->rules(
                ['required','max:200'],
                ['required' => '标题必须填写', 'max' => '标题不能超过200个字符']
            ),

            Field::text('name','缩略名')
            ->onlyOnForms(),

            Field::textArea('description','描述')
            ->rules(
                ['max:200'],
                ['max'=>'描述不能超过200个字符']
            )
            ->onlyOnForms(),

            Field::number('level','排序')
            ->editable(),

            Field::image('cover_ids','封面图')
            ->mode('m')
            ->onlyOnForms(),

            Field::select('pid','父节点')
            ->options(Post::orderedList())
            ->rules(['required'],['required'=>'请选择父节点']),

            Field::editor('content','内容')
            ->onlyOnForms(),

            Field::text('page_tpl','单页模板'),

            Field::datetime('created_at','发布时间')
            ->onlyOnIndex(),

            Field::switch('status','状态')
            ->editable()
            ->trueValue('正常')
            ->falseValue('禁用')
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
            new \App\Admin\Searches\PageCategory,
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
            (new \App\Admin\Actions\CreateLink($this->title()))->onlyOnIndex(),
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\EditLink('编辑'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnIndexTableRow(),
            new \App\Admin\Actions\FormSubmit,
            new \App\Admin\Actions\FormReset,
            new \App\Admin\Actions\FormBack,
            new \App\Admin\Actions\FormExtraBack
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
            'type' => 'PAGE',
            'level' => 0,
            'status' => true
        ];
    }

    /**
     * 保存前回调
     *
     * @param  Request  $request
     * @param  array $submitData
     * @return object
     */
    public function beforeSaving(Request $request, $submitData)
    {
        $submitData['adminid'] = ADMINID;

        return $submitData;
    }
}