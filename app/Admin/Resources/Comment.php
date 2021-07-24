<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class Comment extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '评论';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\Models\Comment';

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
        if($this->isEditing()) {
            $comment = $this->newModel()->where('id', $request->id)->first();

            $fields = [
                Field::hidden('id','ID')
                ->onlyOnForms(),
    
                Field::display('文章')
                ->value($comment->article()->first()->title),

                Field::display('标题')
                ->value($comment['title']),

                Field::display('用户')
                ->value($comment->user()->first()->username),

                Field::image('cover_ids','晒图')
                ->mode('multiple')
                ->onlyOnForms(),

                Field::display('内容')
                ->value($comment['content'])
                ->onlyOnForms(),

                Field::display('顶')
                ->value($comment['ding'])
                ->onlyOnForms(),

                Field::display('踩')
                ->value($comment['cai'])
                ->onlyOnForms(),

                Field::display('评分')
                ->value($comment['rate'])
                ->onlyOnForms(),
    
                Field::display('评论时间')
                ->value($comment['created_at'])
                ->onlyOnForms(),
    
                Field::radio('status','状态')->options([
                    0=>'禁用',
                    1=>'启用',
                    2=>'待审核'
                ])
            ];
        } else {
            $fields = [
                Field::text('article.title','评论对象')
                ->onlyOnIndex(),
    
                Field::text('user.username','用户')
                ->onlyOnIndex(),
    
                Field::text('title','评论标题')
                ->onlyOnIndex(),
    
                Field::text('content','内容')
                ->onlyOnIndex(),
    
                Field::datetime('created_at','评论时间')
                ->onlyOnIndex(),
    
                Field::radio('status','状态')
                ->options([
                    0=>'禁用',
                    1=>'启用',
                    2=>'待审核'
                ])
                ->onlyOnIndex()
            ];
        }

        return $fields;
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
            new \App\Admin\Searches\Status,
            new \App\Admin\Searches\DateTimeRange('created_at', '评论时间')
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
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnTableRow(),
            (new \App\Admin\Actions\EditLink('编辑'))->onlyOnTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnTableRow(),
        ];
    }
}