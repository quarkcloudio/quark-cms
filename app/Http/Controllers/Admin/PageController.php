<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class PageController extends QuarkController
{
    public $title = '单页';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Post)->title($this->title)->tree();
        $grid->column('id','ID');
        $grid->column('title','标题')->link();
        $grid->column('level','排序')->editable()->sorter()->width(100);
        $grid->column('author','作者');
        $grid->column('created_at','发布时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 0, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('edit', '编辑');
            $rowAction->menu('show', '显示');
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
                $model->update(['status'=>0]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',0=>'已禁用'])->placeholder('选择状态')->width(110)->advanced();
            $search->between('created_at', '发布时间')->datetime()->advanced();
        })->expand(false);

        $grid->model()->where('type','PAGE')->paginate(100);

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
        $form = Quark::form(new Post);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');

        $form->text('title','标题')
        ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);
        $form->text('name','别名');
        $form->textArea('description','描述')
        ->rules(['max:190'],['max'=>'名称不能超过190个字符']);

        $form->image('cover_ids','封面图')->mode('multiple');

        $form->number('level','排序')->value(0);

        $categorys[0] = '根节点';
        $getCategorys      = Post::where('type','PAGE')->where('status','>',0)->get()->toArray();
        $categoryTrees     = list_to_tree($getCategorys);
        $categoryTreeLists = tree_to_ordered_list($categoryTrees,0,'title');
        foreach ($categoryTreeLists as $key => $value) {
            $categorys[$value['id']] = $value['title'];
        }

        $form->select('pid','父节点')
        ->options($categorys)
        ->rules(['required'],['required'=>'请选择分类'])
        ->width(200);

        $form->editor('content','内容');
        $form->text('page_tpl','单页模板');
        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        $form->saving(function ($form) {
            $form->request['type'] = 'PAGE';
        });

        return $form;
    }
}
