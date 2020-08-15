<?php

namespace App\Http\Controllers\Admin;

use App\Models\Navigation;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class NavigationController extends QuarkController
{
    public $title = '导航';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Navigation)->title($this->title)->tree();
        $grid->column('id','ID');
        $grid->column('title','标题')->link();
        $grid->column('sort','排序')->editable()->sorter()->width(100);
        $grid->column('cover_id','图标')->image();
        $grid->column('url','链接');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 0, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('editWithModal', '编辑')->withModal('编辑导航',function($modal) {
                $modal->disableFooter();
                $modal->form->ajax('admin/navigation/edit');
            });
            $rowAction->menu('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('createWithModal', '创建')
            ->type('primary')
            ->icon('plus-circle')
            ->withModal('创建导航',function($modal) {
                $modal->disableFooter();
                $modal->form->ajax('admin/navigation/create');
            });
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
            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',0=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110);

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

        })->expand(false);

        $grid->model()
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
        $form = Quark::form(new Navigation);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');
        $form->text('title','标题')
        ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

        $navigations[0] = '根节点';
        $getNavigations = Navigation::where('status',1)->get()->toArray();

        $navigationTrees = list_to_tree($getNavigations,'id','pid','children',0);
        $navigationTreeLists = tree_to_ordered_list($navigationTrees,0,'title','children');

        foreach ($navigationTreeLists as $key => $value) {
            $navigations[$value['id']] = $value['title'];
        }

        $form->select('pid','父节点')
        ->options($navigations)
        ->width(200)
        ->value(0);

        $form->text('url','链接');
        $form->image('cover_id','图标')->mode('single');
        $form->number('sort','排序')->value(0);

        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        return $form;
    }
}
