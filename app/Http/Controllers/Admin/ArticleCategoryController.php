<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use QuarkCMS\QuarkAdmin\Http\Controllers\Controller;
use Quark;

class ArticleCategoryController extends Controller
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
        $table = Quark::table(new Category)->title($this->title)->tree();
        $table->column('id','ID');
        $table->column('title','标题')->editLink();
        $table->column('sort','排序')->editable()->sorter()->width(100);
        $table->column('name','缩略名');
        $table->column('page_num','分页数量');
        $table->column('created_at','创建时间');
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

            return $action;
        });

        // 头部操作
        $table->toolBar()->actions(function($action) {

            // 跳转默认创建页面
            return $action->button('创建'.$this->title)->type('primary')->icon('plus-circle')->createLink();
        });

        // select样式的批量操作
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

                return $action;
            });
        });

        $table->search(function($search) {
            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',0=>'已禁用'])
            ->placeholder('选择状态');

            $search->between('created_at', '创建时间')->datetime();
        });

        $table->model()
        ->where('type','ARTICLE')
        ->orderBy('sort', 'asc')
        ->paginate(1000);

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
        $form = Quark::tabForm(new Category);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->tab('基本', function ($form) {
            $form->hidden('id');

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
            ->value(0);

            $form->textArea('description','描述')
            ->rules(['max:190'],['max'=>'名称不能超过190个字符']);

            $form->number('sort','排序')->value(0);

            $form->switch('status','状态')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(true);

        })->tab('扩展', function ($form) {
            $form->image('cover_id','封面图')->mode('single');
            $form->text('index_tpl','频道模板');
            $form->text('lists_tpl','列表模板');
            $form->text('detail_tpl','详情模板');
            $form->number('page_num','分页数量')->value(10);
        });

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
                return success('操作成功！',frontend_url('admin/articleCategory/index'));
            } else {
                return error('操作失败，请重试！');
            }
        });

        return $form;
    }
}
