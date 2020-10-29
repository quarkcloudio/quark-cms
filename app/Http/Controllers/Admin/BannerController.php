<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\BannerCategory;
use Quark;
use QuarkCMS\QuarkAdmin\Http\Controllers\Controller;

class BannerController extends Controller
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
        $table = Quark::table(new Banner)->title($this->title);
        $table->column('id','ID');
        $table->column('title','标题')->editLink();
        $table->column('sort','排序')->editable()->sorter()->width(100);
        $table->column('category.title','位置');
        $table->column('cover_id','图片')->image();
        $table->column('created_at','添加时间');
        $table->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 0, 'text' => '禁用']
        ])->width(100);
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

        $table->toolBar()->actions(function($action) {
            // 跳转默认创建页面
            $action->button('创建'.$this->title)
            ->type('primary')
            ->icon('plus-circle')
            ->createLink();
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

                $action->item('启用')
                ->withConfirm('确认要启用吗？','启用后数据可以正常使用！')
                ->model()
                ->whereIn('id','{ids}')
                ->update(['status'=>1]);
            });
        });

        $table->search(function($search) {
            $categorys = BannerCategory::where('status',1)->get();
            $options[''] = '全部';
            foreach ($categorys as $key => $value) {
                $options[$value['id']] = $value['title'];
            }

            $search->equal('category_id', '广告位')
            ->select($options)
            ->placeholder('选择广告位');

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',2=>'已禁用'])
            ->placeholder('选择状态');

            $search->between('deadline', '截止时间')->datetime();
        });

        $table->model()->orderBy('id','desc')->paginate(request('pageSize',10));

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
        $form = Quark::form(new Banner);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->hidden('id');

        $form->text('title','标题')
        ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

        $getCategorys = BannerCategory::where('status',1)->get();
        foreach ($getCategorys as $key => $value) {
            $categorys[$value['id']] = $value['title'];
        }

        $form->select('category_id','分类目录')
        ->options($categorys)
        ->rules(['required'],['required'=>'请选择分类']);
        
        $form->text('url','链接')->rules(['required'],['required'=>'请填写链接']);

        $form->image('cover_id','封面图')->mode('single');

        $form->number('sort','排序');

        $form->datetime('deadline','截止时间');

        $form->switch('status','状态')->options([
            'on'  => '是',
            'off' => '否'
        ])->default(true);

        // 编辑页面展示前回调
        $form->editing(function ($form) {
            if(isset($form->initialValues['cover_id'])) {
                $form->initialValues['cover_id'] = get_picture($form->initialValues['cover_id'],0,'all');
            }
        });

        // 保存数据前回调
        $form->saving(function ($form) {
            if(isset($form->data['cover_id'])) {
                $form->data['cover_id'] = $form->data['cover_id']['id'];
            }
        });

        // 保存数据后回调
        $form->saved(function ($form) {
            if($form->model()) {
                return success('操作成功！',frontend_url('admin/banner/index'));
            } else {
                return error('操作失败，请重试！');
            }
        });

        return $form;
    }
}
