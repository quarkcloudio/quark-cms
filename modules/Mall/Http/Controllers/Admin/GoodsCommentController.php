<?php

namespace Modules\Mall\Http\Controllers\Admin;

use Modules\Mall\Models\Comment;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class GoodsCommentController extends QuarkController
{
    public $title = '评论';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Comment)->title($this->title);
        $grid->column('id','ID');
        $grid->column('goods.goods_name','评论商品')->link();
        $grid->column('user.username','用户');
        $grid->column('title','评论标题');
        $grid->column('content','内容');
        $grid->column('created_at','评论时间');
        $grid->column('status','状态')->using(['1'=>'已审核','0'=>'已禁用','2'=>'待审核'])->width(80);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('edit', '编辑');
            $rowAction->menu('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('refresh', '刷新');
        });

        // select样式的批量操作
        $grid->batchActions(function($batch) {
            $batch->option('', '批量操作');
            $batch->option('resume', '审核')->model(function($model) {
                $model->update(['status'=>1]);
            });
            $batch->option('forbid', '禁用')->model(function($model) {
                $model->update(['status'=>0]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'已审核',0=>'已禁用',2=>'待审核'])->placeholder('选择状态')->width(110);

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->between('created_at', '评论时间')->datetime()->advanced();
        })->expand(false);

        $grid->model()->where('type','GOODS')->paginate(10);

        return $grid;
    }
    
    /**
     * 表单页面
     * 
     * @param  Request  $request
     * @return Response
     */
    protected function form()
    {
        $id = request('id');
        
        $comment = Comment::find($id);

        $form = Quark::form(new Comment);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');
        $form->display('商品')->value($comment->goods()->first()->goods_name);
        $form->display('标题')->value($comment['title']);
        $form->display('用户')->value($comment->user()->first()->username);
        $form->image('cover_ids','晒图')->mode('multiple');
        $form->display('内容')->value($comment['content']);
        $form->display('顶')->value($comment['ding']);
        $form->display('踩')->value($comment['cai']);
        if($comment['report']>1) {
            switch ($comment['report']) {
                case '0':
                    $report = '暂无举报';
                    break;
                case '1':
                    $report = '淫秽色情';
                    break;
                case '2':
                    $report = '垃圾广告';
                    break;
                case '3':
                    $report = '违法信息';
                    break;
                default:
                    $report = '未知';
                    break;
            }
            $form->display('举报')->style(['color'=>'#f81d22'])->value($report);
        }

        $form->display('评分')->value($comment['rate']);
        $form->display('评论时间')->value($comment['created_at']);

        $form->radio('status','状态')->options([
            1=>'审核',2=>'禁用',3=>'待审核'
        ]);

        return $form;
    }
}
