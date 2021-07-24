<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use Quark;
use QuarkCMS\QuarkAdmin\Http\Controllers\Controller;

class CommentController extends Controller
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
        $table = Quark::table(new Comment)->title($this->title);
        $table->column('id','ID');
        $table->column('article.title','评论对象')->editLink();
        $table->column('user.username','用户');
        $table->column('title','标题');
        $table->column('content','内容');
        $table->column('created_at','评论时间');
        $table->column('status','状态')->using(['1'=>'已审核','0'=>'已禁用','2'=>'待审核'])->width(80);

        $table->column('actions','操作')
        ->width(120)
        ->actions(function($action,$row) {

            // 根据不同的条件定义不同的A标签形式行为
            if($row['status'] === 1) {
                $action->a('禁用')
                ->withPopconfirm('确认要禁用数据吗？')
                ->model()
                ->where('id','{id}')
                ->update(['status'=>0]);
            } else {
                $action->a('启用')
                ->withPopconfirm('确认要启用数据吗？')
                ->model()
                ->where('id','{id}')
                ->update(['status'=>1]);
            }

            // 跳转默认编辑页面
            $action->a('编辑')->editLink();
            $action->a('删除')
            ->withPopconfirm('确认要删除吗？')
            ->model()
            ->where('id','{id}')
            ->delete();
        });

        // 批量操作
        $table->batchActions(function($action) {
            // 跳转默认编辑页面
            $action->a('批量删除')
            ->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！')
            ->model()
            ->whereIn('id','{ids}')
            ->delete();

            // 下拉菜单形式的行为
            $action->dropdown('更多')->overlay(function($action) {
                $action->item('禁用')
                ->withConfirm('确认要禁用吗？','禁用后数据将无法使用，请谨慎操作！')
                ->model()
                ->whereIn('id','{ids}')
                ->update(['status'=>0]);

                $action->item('审核')
                ->withConfirm('确认要审核吗？','审核后数据可以正常使用！')
                ->model()
                ->whereIn('id','{ids}')
                ->update(['status'=>1]);
            });
        });

        $table->search(function($search) {

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'已审核',0=>'已禁用',2=>'待审核'])->placeholder('选择状态');

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->between('created_at', '评论时间')->datetime();
        });

        $table->model()->where('type','ARTICLE')->orderBy('id','desc')->paginate(request('pageSize',10));

        return $table;
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

        $form->hidden('id');
        $form->display('文章')->value($comment->article()->first()->title);
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

        // 保存数据后回调
        $form->saved(function ($form) {
            if($form->model()) {
                return success('操作成功！',frontend_url('admin/comment/index'));
            } else {
                return error('操作失败，请重试！');
            }
        });

        return $form;
    }
}
