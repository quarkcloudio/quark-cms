<?php

namespace App\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use App\Models\GoodsType;
use Validator;
use DB;
use Quark;

class GoodsTypeController extends QuarkController
{
    public $title = '商品类型';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new GoodsType)->title($this->title);

        $grid->column('id','ID');
        $grid->column('name','商品类型')->link();
        $grid->column('description','类型描述');
        $grid->column('sort','排序')->editable()->sorter()->width(100);
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 2, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(480)->rowActions(function($rowAction) {

            $rowAction->button('attributeCreate', '添加属性')
            ->type('default')
            ->size('small')
            ->link();

            $rowAction->button('specificationCreate', '添加规格')
            ->type('default')
            ->size('small')
            ->link();

            $rowAction->button('attributeIndex', '属性列表')
            ->type('default')
            ->size('small')
            ->link();

            $rowAction->button('specificationIndex', '规格列表')
            ->type('default')
            ->size('small')
            ->link();

            $rowAction->button('edit', '编辑')
            ->type('default')
            ->size('small');

            $rowAction->button('delete', '删除')->model(function($model) {
                $model->delete();
            })->type('default',true)
            ->size('small')
            ->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        },'button');

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
            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',2=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110);

            $search->where('name', '搜索内容',function ($query) {
                $query->where('name', 'like', "%{input}%");
            })->placeholder('搜索内容');

        })->expand(false);

        $grid->model()->paginate(10);

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
        $form = Quark::form(new GoodsType);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');

        $form->text('name','商品类型')
        ->rules(['required','max:190'],['required'=>'类型必须填写','max'=>'类型不能超过190个字符']);

        $form->textArea('description','描述')
        ->rules(['max:190'],['max'=>'描述不能超过190个字符']);

        $form->number('sort','排序')->value(0);

        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        return $form;
    }
}
