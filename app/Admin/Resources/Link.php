<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class Link extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '友情链接';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Link';

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
            Field::hidden('id','ID')
            ->onlyOnForms(),

            Field::text('title','标题')
            ->rules(
                ['required','max:200'],
                ['required' => '标题必须填写', 'max' => '标题不能超过200个字符']
            ),

            Field::image('cover_id','封面图', function() {
                $coverId = \json_decode($this->cover_id,true);

                return is_array($coverId) ? get_picture($coverId['id']) : null;
            }),

            Field::number('sort','排序')
            ->editable()
            ->value(0),

            Field::text('url','链接')
            ->editable(),

            Field::datetime('created_at','添加时间')
            ->onlyOnIndex(),

            Field::switch('status','状态')
            ->editable()
            ->trueValue('正常')
            ->falseValue('禁用')
            ->value(true)
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
            new \App\Admin\Searches\BannerCategory,
            new \App\Admin\Searches\Status,
            new \App\Admin\Searches\DateTimeRange('deadline', '截止时间'),
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
}