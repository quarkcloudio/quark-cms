<?php

namespace App\Http\Controllers\Admin;

use App\Models\Link;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class LinkController extends QuarkController
{
    public $title = '友情链接';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Link)->title($this->title);
        $grid->column('id','ID');
        $grid->column('title','标题')->link();
        $grid->column('sort','排序')->sorter()->editable()->width('80');
        $grid->column('url','链接');
        $grid->column('cover_id','图片')->image();
        $grid->column('created_at','添加时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 2, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('edit', '编辑');
            $rowAction->menu('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('create', '新增');
            $action->button('refresh', '刷新');
        });

        // select样式的批量操作
        $grid->batchActions(function($batch) {
            $batch->option('', '批量操作');
            $batch->option('resume', '启用')->model(function($model) {
                $model->update(['status'=>1]);
            });
            $batch->option('forbid', '禁用')->model(function($model) {
                $model->update(['status'=>2]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',2=>'已禁用'])->placeholder('选择状态')->width(110);

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->between('created_at', '注册时间')->datetime()->advanced();
        })->expand(false);

        $grid->model()->orderBy('sort', 'desc')->paginate(10);

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

        $form = Quark::form(new Link);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');
        $form->text('title','标题')
        ->rules(['required','max:200'],['required'=>'标题必须填写','max'=>'标题不能超过200个字符']);
        $form->text('url','链接');
        $form->image('cover_id','封面图')->button('上传图片');
        $form->number('sort','排序')->value(0);
        $form->switch('status','状态')->options([
            'on'  => '正常',
            'off' => '禁用'
        ])->default(true);

        return $form;
    }
}
