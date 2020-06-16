<?php

namespace App\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use App\Models\GoodsLayout;
use Validator;
use DB;
use Quark;

class GoodsLayoutController extends QuarkController
{
    public $title = '详情版式';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new GoodsLayout)->title($this->title);

        $grid->column('id','ID');
        $grid->column('layout_name','版式名称')->link();
        $grid->column('position','模板位置')->using([1=>'详情顶部',2=>'详情底部',3=>'包装清单',4=>'售后保障']);
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

            $options[''] = '全部';
            $options[1] = '详情顶部';
            $options[2] = '详情底部';
            $options[3] = '包装清单';
            $options[4] = '售后保障';

            $search->equal('position', '模板位置')->select($options)->placeholder('选择位置')->width(110);

            $search->where('layout_name', '搜索内容',function ($query) {
                $query->where('layout_name', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',2=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110)->advanced();

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
        $form = Quark::form(new GoodsLayout);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');

        $form->text('layout_name','版式名称')
        ->rules(['required','max:190'],['required'=>'名称必须填写','max'=>'名称不能超过190个字符']);

        $form->radio('position','模板位置')
        ->options([1 => '详情顶部', 2 => '详情底部', 3 => '包装清单', 4 => '售后保障'])
        ->default(1);

        $form->editor('content','内容');

        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        return $form;
    }
}
