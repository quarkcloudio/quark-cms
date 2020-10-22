<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Category;
use Quark;
use QuarkCMS\QuarkAdmin\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public $title = '文章';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $table = Quark::table(new Post)->title($this->title);
        $table->column('id','ID');
        $table->column('title','标题')->editLink();
        $table->column('level','排序')->editable()->sorter()->width(100);
        $table->column('author','作者');
        $table->column('category.title','分类');
        $table->column('created_at','发布时间');
        $table->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 0, 'text' => '禁用']
        ])->width(100);

        $table->column('actions','操作')->width(120)->actions(function($action,$row) {

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
            $action->button('创建'.$this->title)->type('primary')->icon('plus-circle')->createLink();
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

        // 搜索
        $table->search(function($search) {

            $categorys = Category::where('type','ARTICLE')->where('status',1)->get();
            $options[''] = '全部';
            foreach ($categorys as $key => $value) {
                $options[$value['id']] = $value['title'];
            }

            $search->equal('category_id', '文章分类')->select($options)->placeholder('选择分类')->width(110);

            $search->where('title', '搜索内容',function ($model) {
                $model->where('title', 'like', "%{input}%");
            })->placeholder('名称');

            $search->equal('status', '所选状态')
            ->select([''=>'全部', 1=>'正常', 0=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110);

            $search->between('created_at', '发布时间')->datetime();
        });

        if(ADMINID == 1) {
            $table->model()->where('type','ARTICLE')->orderBy('id','desc')->paginate(request('pageSize',10));
        } else {
            $table->model()->where('adminid',ADMINID)->where('type','ARTICLE')->orderBy('id','desc')->paginate(request('pageSize',10));
        }

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
        $form = Quark::tabForm(new Post);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->tab('基本', function ($form) {
            $form->hidden('id');

            $form->text('title','标题')
            ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

            $form->textArea('description','描述')
            ->rules(['max:190'],['max'=>'描述不能超过190个字符']);

            $form->text('author','作者');

            $form->text('source','来源');

            $form->checkbox('position','推荐位')->options([
                1 => '首页推荐',
                2 => '频道推荐',
                3 => '列表推荐',
                4 => '详情推荐'
            ]);

            $form->radio('show_type','展现形式')->options([
                1 => '无图',
                2 => '单图（小）',
                3 => '多图',
                4 => '单图（大）'
            ])->when('in',[2,4],function($form) {
                $form->image('cover_ids','封面图')->mode('m')->limitNum(1);
            })->when(3,function($form) {
                $form->image('cover_ids','封面图')->mode('m');
            });

            $categorys = [];
            $getCategorys = Category::where('type','ARTICLE')->where('status',1)->get()->toArray();
            $categoryTrees = list_to_tree($getCategorys,'id','pid','children',0);
            $categoryTreeLists = tree_to_ordered_list($categoryTrees,0,'name','children');

            foreach ($categoryTreeLists as $key => $value) {
                $categorys[$value['id']] = $value['title'];
            }

            $form->select('category_id','分类目录')
            ->options($categorys)
            ->rules(['required'],['required'=>'请选择分类']);

            $form->editor('content','内容');

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
            $form->file('file_id','附件');

            $form->switch('comment_status','允许评论')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(true);
        });

        $form->saving(function ($form) {
            $form->request['adminid'] = ADMINID;
        });

        // 保存数据后回调
        $form->saved(function ($form) {
            if($form->model()) {
                return success('操作成功！',frontend_url('admin/article/index'));
            } else {
                return error('操作失败，请重试！');
            }
        });

        return $form;
    }
}
