<?php

namespace Modules\Mall\Http\Controllers\Admin;

use Modules\Mall\Models\Printer;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class PrinterController extends QuarkController
{
    public $title = '打印机';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Printer)->title($this->title);
        $grid->column('id','ID');
        $grid->column('name','名称')->link();
        $grid->column('phone','手机卡号');
        $grid->column('client_id','应用ID');
        $grid->column('client_secret','应用密钥');
        $grid->column('created_at','添加时间');
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
            $search->where('name', '搜索内容',function ($query) {
                $query->where('name', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',0=>'已禁用'])->placeholder('选择状态')->width(110)->advanced();
            $search->between('created_at', '添加时间')->datetime()->advanced();
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
        $form = Quark::form(new Printer);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');

        $form->text('name','名称')
        ->rules(['required','max:190'],['required'=>'名称必须填写','max'=>'名称不能超过190个字符']);
        
        $form->text('phone','手机卡号')
        ->rules(['required','max:11'],['required'=>'手机卡号必须填写','max'=>'手机卡号不能超过11个字符']);

        $form->text('client_id','应用ID')
        ->rules(['required','max:190'],['required'=>'应用ID必须填写','max'=>'应用ID不能超过190个字符']);

        $form->text('client_secret','应用密钥')
        ->rules(['required','max:190'],['required'=>'应用密钥必须填写','max'=>'应用密钥不能超过190个字符']);

        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        return $form;
    }
}