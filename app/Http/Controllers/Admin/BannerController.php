<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\BannerCategory;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class BannerController extends QuarkController
{
    public $title = '广告';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Banner)->title($this->title);
        $grid->column('id','ID');
        $grid->column('title','标题')->link();
        $grid->column('sort','排序')->editable()->sorter()->width(100);
        $grid->column('category.title','位置');
        $grid->column('cover_id','图片')->image();
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
            $categorys = BannerCategory::where('status',1)->get();
            $options[''] = '全部';
            foreach ($categorys as $key => $value) {
                $options[$value['id']] = $value['title'];
            }

            $search->equal('category_id', '广告位')->select($options)->placeholder('选择广告位')->width(110);
            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',2=>'已禁用'])->placeholder('选择状态')->width(110)->advanced();
            $search->between('deadline', '截止时间')->datetime()->advanced();
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
        $form = Quark::form(new Banner);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');

        $form->text('title','标题')
        ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

        $getCategorys = BannerCategory::where('status',1)->get();
        foreach ($getCategorys as $key => $value) {
            $categorys[$value['id']] = $value['title'];
        }

        $form->select('category_id','分类目录')
        ->options($categorys)
        ->rules(['required'],['required'=>'请选择分类'])
        ->width(200);
        
        $form->text('url','链接');

        $form->image('cover_id','封面图')->mode('single');

        $form->number('sort','排序');

        $form->datetime('deadline','截止时间');

        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        return $form;
    }
}
