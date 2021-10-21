<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;
use QuarkCMS\Quark\Facades\TabPane;
use App\Models\Category;

class Article extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '文章';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Post';

    /**
     * 分页
     *
     * @var int|bool
     */
    public static $perPage = 10;

    /**
     * 列表查询
     *
     * @param  Request  $request
     * @return object
     */
    public static function indexQuery(Request $request, $query)
    {
        return $query->orderBy('id','desc')->where('type','ARTICLE');
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

            Field::hidden('adminid','ADMINID')
            ->onlyOnForms(),

            Field::text('title','标题')
            ->rules(
                ['required','max:200'],
                ['required' => '标题必须填写', 'max' => '标题不能超过200个字符']
            ),

            Field::textArea('description','描述')
            ->rules(
                ['max:200'],
                ['max'=>'描述不能超过200个字符']
            )
            ->onlyOnForms(),

            Field::text('author','作者'),

            Field::number('level','排序')
            ->editable()
            ->onlyOnIndex(),

            Field::text('source','来源')
            ->onlyOnForms(),
            
            Field::checkbox('position','推荐位')
            ->options([
                1 => '首页推荐',
                2 => '频道推荐',
                3 => '列表推荐',
                4 => '详情推荐'
            ])
            ->onlyOnForms(),

            Field::radio('show_type','展现形式')
            ->options([
                1 => '无图',
                2 => '单图（小）',
                3 => '多图',
                4 => '单图（大）'
            ])->when('in', [2, 4], function() {

                return Field::image('cover_ids','封面图')
                ->mode('m')
                ->limitNum(1)
                ->onlyOnForms();
            })->when(3, function() {
                
                return Field::image('cover_ids','封面图')
                ->mode('m')
                ->onlyOnForms();
            })
            ->onlyOnForms(),

            Field::select('category_id','分类目录')
            ->options(Category::orderedList('ARTICLE'))
            ->rules(['required'],['required'=>'请选择分类']),

            Field::editor('content','内容')
            ->onlyOnForms(),

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
            Field::text('name','缩略名')
            ->onlyOnForms(),

            Field::number('level','排序')
            ->onlyOnForms(),

            Field::number('view','浏览量')
            ->onlyOnForms(),

            Field::number('comment','评论量')
            ->onlyOnForms(),

            Field::text('password','访问密码')
            ->onlyOnForms(),

            Field::file('file_ids','附件')
            ->onlyOnForms(),

            Field::switch('comment_status','允许评论')
            ->editable()
            ->trueValue('是')
            ->falseValue('否')
            ->onlyOnForms(),

            Field::datetime('created_at','发布时间')
            ->onlyOnIndex(),

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
     * 表单显示前回调
     * 
     * @param Request $request
     * @return array
     */
    public function beforeCreating(Request $request)
    {
        $admin = \QuarkCMS\QuarkAdmin\Models\Admin::where('id',ADMINID)->first();

        // 初始化数据
        return [
            'author' => $admin['nickname'],
            'level' => 0,
            'view' => 0,
            'show_type' => 1,
            'comment' => 0,
            'status' => true,
            'comment_status' => true
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