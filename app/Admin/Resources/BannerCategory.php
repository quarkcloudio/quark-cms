<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class BannerCategory extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '广告位';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\BannerCategory';

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
        return [
            Field::hidden('id','ID')
            ->onlyOnForms(),

            Field::text('title','标题')
            ->editable()
            ->rules(
                ['required', 'max:200'],
                ['required' => '标题必须填写', 'max' => '标题不能超过200个字符']
            ),

            Field::text('name','缩略名')
            ->editable()
            ->rules(
                ['required','max:200'],
                ['required' => '缩略名必须填写', 'max'=>'缩略名不能超过200个字符']
            ),

            Field::number('width','宽度'),

            Field::number('height','高度'),

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
            (new \App\Admin\Actions\CreateModal($this->title()))->onlyOnIndex(),
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\EditModal('编辑'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnIndexTableRow(),
            new \App\Admin\Actions\FormSubmit,
            new \App\Admin\Actions\FormReset,
            new \App\Admin\Actions\FormBack,
            new \App\Admin\Actions\FormExtraBack
        ];
    }
}