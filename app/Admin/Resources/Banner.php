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
     * 字段
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $api = (new \App\Admin\Actions\Suggest)->api();

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
            })->rules(
                ['required'],
                ['required' => '图片必须上传']
            ),

            Field::select('category_id','位置')
            ->options(BannerCategory::list())
            ->rules(['required'],['required'=>'请选择分类']),

            Field::radio('url_type','链接类型')
            ->options([
                1 => '文章',
                2 => '单页',
                3 => '分类目录',
                4 => '外部链接'
            ])
            ->when(1, function() use ($api) {

                return Field::search('article_id','选择文章')
                ->api($api.'?model=article')
                ->onlyOnForms();
            })
            ->when(2, function() use ($api) {
                
                return Field::search('page_id','选择单页')
                ->api($api.'?model=page')
                ->onlyOnForms();
            })
            ->when(3, function() use ($api) {
                
                return Field::search('url_category_id','分类目录')
                ->api($api.'?model=category')
                ->onlyOnForms();
            })
            ->when(4, function() use ($api) {
                
                return Field::text('url','链接')->onlyOnForms();
            })
            ->default(1),

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
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\EditLink('编辑'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\FormSubmit),
            (new \App\Admin\Actions\FormReset),
            (new \App\Admin\Actions\FormBack),
            (new \App\Admin\Actions\FormExtraBack),
            new \App\Admin\Actions\Suggest
        ];
    }

    /**
     * 保存前回调
     *
     * @param  Request  $request
     * @param  array $data
     * @return object
     */
    public function beforeEditing(Request $request, $data)
    {
        switch ($data['url_type']) {
            case 1:
                // 文章
                $data['article_id'] = $data['url'];
                break;
            case 2:
                // 单页
                $data['page_id'] = $data['url'];
                break;
            case 3:
                // 分类目录
                $data['url_category_id'] = $data['url'];
                break;
            case 4:
                // 其他链接
                $data['url'] = $data['url'];
                break;
            default:
                // 文章
                $data['article_id'] = $data['url'];
                break;
        }

        if($data['url_type'] !== 4) {
            unset($data['url']);
        }

        return $data;
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
        if(isset($submitData['article_id'])) {
            $submitData['url'] = $submitData['article_id'];
            unset($submitData['article_id']);
        }

        if(isset($submitData['page_id'])) {
            $submitData['url'] = $submitData['page_id'];
            unset($submitData['page_id']);
        }

        if(isset($submitData['url_category_id'])) {
            $submitData['url'] = $submitData['url_category_id'];
            unset($submitData['url_category_id']);
        }

        return $submitData;
    }
}