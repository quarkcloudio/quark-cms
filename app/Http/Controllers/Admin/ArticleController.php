<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Category;
use QuarkCMS\QuarkAdmin\Models\Admin;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Quark;

class ArticleController extends QuarkController
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
        $grid = Quark::grid(new Post)->title($this->title);
        $grid->column('id','ID');
        $grid->column('title','标题')->link();
        $grid->column('level','排序')->editable()->sorter()->width(100);
        $grid->column('author','作者');
        $grid->column('category.title','分类');
        $grid->column('created_at','发布时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 2, 'text' => '禁用']
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
                $model->update(['status'=>2]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {
            $categorys = Category::where('type','ARTICLE')->get();
            $options[''] = '全部';
            foreach ($categorys as $key => $value) {
                $options[$value['id']] = $value['title'];
            }

            $search->equal('category_id', '文章分类')->select($options)->placeholder('选择分类')->width(110);
            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',2=>'已禁用'])->placeholder('选择状态')->width(110)->advanced();
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
        $form = Quark::form(new Post);

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

            $form->radio('show_type','展现形式')->options([
                1 => '无图',
                2 => '单图（小）',
                3 => '多图',
                4 => '单图（大）'
            ]);

            $form->image('cover_ids','封面图')->mode('multiple');

            $getCategorys = Category::where('type','ARTICLE')->get();
            foreach ($getCategorys as $key => $value) {
                $categorys[$value['id']] = $value['title'];
            }

            $form->select('category_id','分类目录')
            ->options($categorys)
            ->rules(['required'],['required'=>'请选择分类'])
            ->width(200);

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

        return $form;
    }

    /**
     * 我发布的文章列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    public function myPublished(Request $request)
    {
        // 获取参数
        $current   = intval($request->get('current',1));
        $pageSize  = intval($request->get('pageSize',10));
        $search    = $request->get('search');
            
        // 定义对象
        $query = Post::query();

        if(ADMINID) {
            $query->where('posts.adminid',ADMINID);
        }

        // 查询
        if(!empty($search)) {
            // 标题
            if(isset($search['title'])) {
                $query->where('posts.title','like','%'.$search['title'].'%');
            }

            // 分类
            if(isset($search['category_id'])) {
                if(!empty($search['category_id'])) {
                    $query->where('posts.category_id',$search['category_id']);
                }
            }

            // 状态
            if(isset($search['status'])) {
                if(!empty($search['status'])) {
                    $query->where('posts.status',$search['status']);
                }
            }
        }

        // 查询数量
        $total = $query
        ->where('posts.status', '>', 0)
        ->where('posts.type', 'ARTICLE')
        ->count();

        // 查询列表
        $lists = $query
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->skip(($current-1)*$pageSize)
        ->take($pageSize)
        ->where('posts.status', '>', 0)
        ->orderBy('id', 'desc')
        ->select('posts.*','categories.name as category_name','categories.title as category_title')
        ->get()
        ->toArray();

        foreach ($lists as $key => $value) {
            if(empty($value['name'])) {
                $lists[$key]['name'] = '暂无';
            }
        }

        // 默认页码
        $pagination['defaultCurrent'] = 1;
        // 当前页码
        $pagination['current'] = $current;
        // 分页数量
        $pagination['pageSize'] = $pageSize;
        // 总数量
        $pagination['total'] = $total;

        $categorys         = Category::where('type','ARTICLE')->get()->toArray();
        $categoryTrees     = Helper::listToTree($categorys);
        $categoryTreeLists = Helper::treeToOrderList($categoryTrees,0,'title');

        $getCategorys = [];

        $getCategorys[0]['name'] = '所有分类';
        $getCategorys[0]['value'] = '0';

        foreach ($categoryTreeLists as $key => $categoryTreeList) {
            $getCategorys[$key+1]['name'] = $categoryTreeList['title'];
            $getCategorys[$key+1]['value'] = $categoryTreeList['id'];
        }

        $lists = Helper::listsFormat($lists);

        $status = [
            [
                'name'=>'所有状态',
                'value'=>'0',
            ],
            [
                'name'=>'正常',
                'value'=>'1',
            ],
            [
                'name'=>'禁用',
                'value'=>'2',
            ],
        ];

        $searchs = [
            Select::make('分类','categorys')->option($getCategorys)->value('0'),
            Select::make('状态','status')->option($status)->value('0'),
            Input::make('搜索内容','title'),
            Button::make('搜索')->onClick('search'),
        ];

        $columns = [
            Column::make('ID','id'),
            Column::make('标题','title')->withA('admin/article/edit'),
            Column::make('作者','author'),
            Column::make('分类','category_title'),
            Column::make('状态','status')->withTag("text === '已禁用' ? 'red' : 'blue'"),
            Column::make('发布时间','created_at'),
        ];

        $data = $this->listBuilder($columns,$lists,$pagination,$searchs);

        if(!empty($data)) {
            return $this->success('获取成功！','',$data);
        } else {
            return $this->success('获取失败！');
        }
    }
}
