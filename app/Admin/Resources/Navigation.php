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
        $api = (new \App\Admin\Actions\Suggest)->api();

        return [
            Field::hidden('id','ID')
            ->onlyOnForms(),

            Field::text('title','标题')
            ->rules(
                ['required'],
                ['required' => '标题必须填写']
            ),

            Field::select('pid','父节点')
            ->options(\App\Models\Navigation::orderedList())
            ->default(0)
            ->onlyOnForms(),

            Field::radio('url_type','链接类型')
            ->options([
                1 => '文章',
                2 => '单页',
                3 => '分类目录',
                4 => '外部链接'
            ])
            ->when(1, function() use ($api) {

                return Field::search('article_id','选择文章')
                ->api($api.'model=article')
                ->onlyOnForms();
            })
            ->when(2, function() use ($api) {
                
                return Field::search('page_id','选择单页')
                ->api($api.'model=page')
                ->onlyOnForms();
            })
            ->when(3, function() use ($api) {
                
                return Field::search('category_id','分类目录')
                ->api($api.'model=category')
                ->onlyOnForms();
            })
            ->when(4, function() use ($api) {
                
                return Field::text('url','链接')->onlyOnForms();
            })
            ->default(1),

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
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\EditDrawer('编辑'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnIndexTableRow(),
            new \App\Admin\Actions\Suggest
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
                $data['category_id'] = $data['url'];
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

        if(isset($submitData['category_id'])) {
            $submitData['url'] = $submitData['category_id'];
            unset($submitData['category_id']);
        }

        return $submitData;
    }
}