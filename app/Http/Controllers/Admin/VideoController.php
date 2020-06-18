<?php

namespace App\Http\Controllers\Admin;

use App\Models\Video;
use App\Models\Category;
use QuarkCMS\QuarkAdmin\Models\Admin;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class VideoController extends QuarkController
{
    public $title = '视频';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Video)->title($this->title);
        $grid->column('id','ID');
        $grid->column('title','标题')->link();
        $grid->column('level','排序')->editable()->sorter()->width(100);
        $grid->column('author','作者');
        $grid->column('category.title','分类');
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
            $categorys = Category::where('type','VIDEO')->where('status',1)->get();
            $options[''] = '全部';
            foreach ($categorys as $key => $value) {
                $options[$value['id']] = $value['title'];
            }

            $search->equal('category_id', '视频分类')->select($options)->placeholder('选择分类')->width(110);
            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',0=>'已禁用'])->placeholder('选择状态')->width(110)->advanced();
            $search->between('created_at', '发布时间')->datetime()->advanced();
        })->expand(false);

        if(ADMINID == 1) {
            $grid->model()->paginate(10);
        } else {
            $grid->model()->where('adminid',ADMINID)->paginate(10);
        }

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
        $form = Quark::form(new Video);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->tab('基本', function ($form) {
            $form->id('id','ID');

            $form->text('title','标题')
            ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

            $form->textArea('description','描述')
            ->rules(['max:190'],['max'=>'名称不能超过190个字符']);

            $form->text('author','作者');

            $form->text('source','来源');

            $form->checkbox('position','推荐位')->options([
                1 => '首页推荐',
                2 => '频道推荐',
                3 => '列表推荐',
                4 => '详情推荐'
            ]);

            $form->image('cover_ids','封面图')->mode('multiple');

            $getCategorys = Category::where('type','ARTICLE')->where('status',1)->get();
            foreach ($getCategorys as $key => $value) {
                $categorys[$value['id']] = $value['title'];
            }

            $form->select('category_id','分类目录')
            ->options($categorys)
            ->rules(['required'],['required'=>'请选择分类'])
            ->width(200);

            $form->file('path','视频文件')->limitType(['video/mp4'])->limitNum(1)->limitSize(200);

            $form->number('duration','视频时长')->value(0);

            $form->switch('status','状态')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(true);

        })->tab('扩展', function ($form) {
            $form->text('name','缩略名');
            $form->number('level','排序')->value(0);
            $form->number('view','浏览量')->value(0);
            $form->number('comment','评论量')->value(0);
            $form->text('password','访问密码');
            $form->switch('comment_status','允许评论')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(true);
        });

        $form->saving(function ($form) {
            $form->request['adminid'] = ADMINID;
        });

        return $form;
    }
}
