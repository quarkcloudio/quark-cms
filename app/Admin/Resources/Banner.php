<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;
use App\Models\BannerCategory;

class Banner extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '广告';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Banner';

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
        return $query->orderBy('id','desc');
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
                ['required','max:200'],
                ['required' => '标题必须填写', 'max' => '标题不能超过200个字符']
            ),

            Field::number('sort','排序')
            ->editable()
            ->value(0),

            Field::image('cover_id','图片', function() {
                $coverId = \json_decode($this->cover_id, true);

                return $coverId ? get_picture($coverId['id']) : null;
            }),

            Field::select('category_id','位置')
            ->options(BannerCategory::list())
            ->rules(['required'],['required'=>'请选择分类']),

            Field::datetime('created_at','添加时间')
            ->onlyOnIndex(),

            Field::datetime('deadline','截止时间'),

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
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnTableRow(),
            (new \App\Admin\Actions\EditLink('编辑'))->onlyOnTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnTableRow(),
        ];
    }
}