<?php

namespace Modules\Mall\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use Modules\Mall\Models\GoodsBrand;
use Validator;
use DB;
use Quark;

class GoodsBrandController extends QuarkController
{
    public $title = '品牌';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new GoodsBrand)->title($this->title);

        $grid->column('id','ID');
        $grid->column('logo','Logo')->image();
        $grid->column('name','品牌名称')->link();
        $grid->column('site_url','品牌网址');
        $grid->column('sort','排序')->editable()->sorter()->width(100);
        $grid->column('is_recommend','推荐')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '是'],
            'off' => ['value' => 2, 'text' => '否']
        ])->width(100);
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 0, 'text' => '禁用']
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
        $form = Quark::form(new GoodsBrand);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');

        $form->text('name','品牌名称')
        ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

        $form->image('logo','Logo')->mode('single');

        $form->image('promotion_image','品牌推广图')->mode('single');

        $form->text('letter','品牌名称首字母');

        $form->textArea('description','品牌描述')
        ->rules(['max:190'],['max'=>'描述不能超过190个字符']);

        $form->text('site_url','品牌网址');

        $form->number('sort','排序')->value(0);

        $form->switch('is_recommend','是否推荐')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        return $form;
    }
}
