<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class ArticleCategoryController extends QuarkController
{
    public $title = '文章分类';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Category)->title($this->title)->tree();
        $grid->column('id','ID');
        $grid->column('title','标题')->link();
        $grid->column('sort','排序')->editable()->sorter()->width(100);
        $grid->column('name','缩略名');
        $grid->column('page_num','分页数量');
        $grid->column('created_at','创建时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 2, 'text' => '禁用']
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
                $model->update(['status'=>2]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {
            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',2=>'已禁用'])->placeholder('选择状态')->width(110)->advanced();
            $search->between('created_at', '创建时间')->datetime()->advanced();
        })->expand(false);

        $grid->model()
        ->where('type','ARTICLE')
        ->orderBy('sort', 'asc')
        ->paginate(1000);

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
        $form = Quark::form(new Category);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->tab('基本', function ($form) {
            $form->id('id','ID');

            $form->text('title','标题')
            ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

            $form->text('name','缩略名');

            $categorys[0] = '根目录';
            $getCategorys = Category::where('type','ARTICLE')->where('status',1)->get()->toArray();

            $categoryTrees = list_to_tree($getCategorys,'id','pid','children',0);
            $categoryTreeLists = tree_to_ordered_list($categoryTrees,0,'name','children');

            foreach ($categoryTreeLists as $key => $value) {
                $categorys[$value['id']] = $value['title'];
            }

            $form->select('pid','父节点')
            ->options($categorys)
            ->width(200);

            $form->textArea('description','描述')
            ->rules(['max:190'],['max'=>'名称不能超过190个字符']);

            $form->number('sort','排序')->value(0);

            $form->switch('status','状态')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(true);

        })->tab('扩展', function ($form) {
            $form->image('cover_ids','封面图')->mode('single');
            $form->text('index_tpl','频道模板');
            $form->text('lists_tpl','列表模板');
            $form->text('detail_tpl','详情模板');
            $form->number('page_num','分页数量')->value(10);
        });

        return $form;
    }
}
