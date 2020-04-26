<?php

namespace App\Http\Controllers\Admin;

use App\User;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AccountLog;
use Quark;
use Validator;
use DB;

class UserController extends QuarkController
{
    public $title = '用户';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new User)->title($this->title);
        $grid->column('id','ID');
        $grid->column('avatar','头像')->image();
        $grid->column('username','用户名')->link();
        $grid->column('nickname','昵称');
        $grid->column('phone','手机号');
        $grid->column('email','邮箱');
        $grid->column('created_at','注册时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 2, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(260)->rowActions(function($rowAction) {

            $rowAction->button('recharge', '充值')
            ->type('default')
            ->size('small')
            ->withModal('用户充值',function($modal) {
                $modal->disableFooter();
                $modal->form->ajax('admin/user/recharge');
            });

            $rowAction->button('show', '显示')
            ->type('default')
            ->size('small');

            $rowAction->button('edit', '编辑')
            ->type('primary')
            ->size('small');

            $rowAction->button('delete', '删除')
            ->type('default',true)
            ->size('small')
            ->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');

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

            $search->where('usernameOrNickname', '搜索内容',function ($query) {
                $query->where('username', 'like', "%{input}%")->orWhere('nickname', 'like', "%{input}%")->orWhere('phone', 'like', "%{input}%");
            })->placeholder('用户名/手机号/昵称');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',2=>'已禁用'])->placeholder('选择状态')->width(110)->advanced();
            
            $search->between('created_at', '注册时间')->datetime()->advanced();
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
        $id = request('id');

        $form = Quark::form(new User);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->id('id','ID');

        $form->image('avatar','头像')->button('上传头像');

        $form->text('username','用户名')
        ->rules(['required','min:6','max:20'],['required'=>'用户名必须填写','min'=>'用户名不能少于6个字符','max'=>'用户名不能超过20个字符'])
        ->creationRules(["unique:users"],['unique'=>'用户名已经存在'])
        ->updateRules(["unique:users,username,{{id}}"],['unique'=>'用户名已经存在']);

        $form->text('nickname','昵称')
        ->rules(['required','max:20'],['required'=>'昵称必须填写','max'=>'昵称不能超过20个字符']);

        $form->radio('sex','性别')
        ->options(['1' => '男', '2'=> '女'])
        ->default(1);

        $form->text('email','邮箱')
        ->rules(['required','email','max:255'],['required'=>'邮箱必须填写','email'=>'邮箱格式错误','max'=>'邮箱不能超过255个字符'])
        ->creationRules(["unique:users"],['unique'=>'邮箱已经存在',])
        ->updateRules(["unique:users,email,{{id}}"],['unique'=>'邮箱已经存在']);

        $form->text('phone','手机号')
        ->rules(['required','max:11'],['required'=>'手机号必须填写','max'=>'手机号不能超过11个字符'])
        ->creationRules(["unique:users"],['unique'=>'手机号已经存在'])
        ->updateRules(["unique:users,phone,{{id}}"],['unique'=>'手机号已经存在']);

        $form->text('password','密码')
        ->creationRules(["required"],['required'=>'密码不能为空']);

        $form->switch('status','状态')->options([
            'on'  => '正常',
            'off' => '禁用'
        ])->default(true);

        return $form;
    }

    /**
     * 保存方法
     * 
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $requestJson    =   $request->getContent();
        $requestData    =   json_decode($requestJson,true);

        // 删除modelName
        unset($requestData['id']);
        unset($requestData['actionUrl']);

        // 表单验证错误提示信息
        $messages = [
            'unique' => '已经存在',
        ];

        // 表单验证规则
        $rules = [
            'username' => [Rule::unique('users')],
            'email' =>  [Rule::unique('users')],
            'phone' =>  [Rule::unique('users')],
        ];

        // 进行验证
        $validator = Validator::make($requestData,$rules,$messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            foreach($errors as $key => $value) {
                if($key === 'username') {
                    $errorMsg = '用户名'.$value[0];
                }

                if($key === 'email') {
                    $errorMsg = '邮箱'.$value[0];
                }

                if($key === 'phone') {
                    $errorMsg = '手机号'.$value[0];
                }
            }

            return error($errorMsg);
        }

        if (!empty($requestData['password'])) {
            $requestData['password'] = bcrypt($requestData['password']);
        }

        $result = User::create($requestData);

        if ($result) {
            return success('操作成功！','/quark/engine?api=admin/user/index&component=table');
        } else {
            return error('操作失败！');
        }
    }

    /**
     * 保存编辑数据
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $requestJson    =   $request->getContent();
        $requestData    =   json_decode($requestJson,true);

        // 删除modelName
        unset($requestData['actionUrl']);

        // 表单验证错误提示信息
        $messages = [
            'unique' => '已经存在',
        ];

        // 表单验证规则
        $rules = [
            'username' => [Rule::unique('users')->ignore($requestData['id'])],
            'email' =>  [Rule::unique('users')->ignore($requestData['id'])],
            'phone' =>  [Rule::unique('users')->ignore($requestData['id'])],
        ];

        // 进行验证
        $validator = Validator::make($requestData,$rules,$messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            foreach($errors as $key => $value) {
                if($key === 'username') {
                    $errorMsg = '用户名'.$value[0];
                }

                if($key === 'email') {
                    $errorMsg = '邮箱'.$value[0];
                }

                if($key === 'phone') {
                    $errorMsg = '手机号'.$value[0];
                }
            }

            return error($errorMsg);
        }

        if (!empty($requestData['password'])) {
            $requestData['password'] = bcrypt($requestData['password']);
        }

        $result = User::where('id',$requestData['id'])->update($requestData);

        if ($result) {
            return success('操作成功！','/quark/engine?api=admin/user/index&component=table');
        } else {
            return error('操作失败！');
        }
    }

    /**
     * 详情页面
     * 
     * @param  Request  $request
     * @return Response
     */
    protected function detail($id)
    {
        $show = Quark::show(User::findOrFail($id)->toArray())->title('详情页');

        $show->field('id','ID');
        $show->field('avatar','头像')->image();
        $show->field('username','用户名');
        $show->field('nickname','昵称');
        $show->field('sex','性别');
        $show->field('created_at','注册时间');
        $show->field('last_login_time','登录时间');
        $show->field('last_login_ip','登录IP');
        $show->field('status','状态');

        //渲染前回调
        $show->rendering(function ($show) {
            $show->data['avatar'] = get_picture($show->data['avatar']);
            $show->data['sex'] == 1 ? $show->data['sex'] = '男' : $show->data['sex'] = '女';

            if(empty($show->data['last_login_time'])) {
                $show->data['last_login_time'] = '暂无';
            }

            if(empty($show->data['last_login_ip'])) {
                $show->data['last_login_ip'] = '暂无';
            }
        });

        return $show;
    }

    /**
    * 用户充值
    *
    * @param  Request  $request
    * @return Response
    */
    public function recharge(Request $request)
    {
        if($request->isMethod('post')) {
            $id        =   $request->json('id');
            $money     =   $request->json('money',0);
            $score     =   $request->json('score',0);
            $remark    =   $request->json('remark');
    
            if (empty($remark)) {
                return $this->error('理由必需填写！');
            }
    
            // 开启事务
            DB::beginTransaction();
            try {
                $result = true;
    
                if (!empty($money)) {
                    User::where('id',$id)->increment('money', $money);
                }
    
                if (!empty($score)) {
                    User::where('id',$id)->increment('score', $score);
                }
    
                $data['adminid'] = ADMINID;
                $data['uid'] = $id;
                $data['money'] = $money;
                $data['score'] = $score;
                $data['type'] = 3;
                $data['remark'] = $remark;
    
                AccountLog::create($data);
                
                DB::commit();
            } catch (\Exception $e) {
                $result = false;
                DB::rollBack();
            }
    
            if ($result) {
                return success('操作成功！');
            } else {
                return error('操作失败！');
            }

        } else {

            $id = request('id');
            $user = User::where('id',$id)->first();
            $form = Quark::form();

            $layout['labelCol']['span'] = 4;
            $layout['wrapperCol']['span'] = 20;
            $form->layout($layout);

            $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
            $form->title($title);

            $form->setAction('admin/user/recharge');

            $form->id('id','ID')->value($id);
            $form->display('充值用户')->value($user['username'].'（'.$user['nickname'].'）');
            $form->display('当前余额')->style(['color'=>'#f81d22'])->value($user['money']);
            $form->display('当前积分')->style(['color'=>'#f81d22'])->value($user['score']);
            $form->number('money','余额充值')->extra('正数为充值，负数为扣款')->value(0);
            $form->number('score','积分充值')->extra('正数为充值，负数为扣除')->value(0);
            $form->textArea('remark','充值理由')
            ->rules(['required','max:190'],['required'=>'充值理由必须填写','max'=>'充值理由不能超过190个字符']);

            $content = Quark::content()->body(['form'=>$form->render()]);

            return success('获取成功！','',$content);
        }
    }

    /**
     * 用户建议搜索列表
     *
     * @param  Request  $request
     * @return Response
     */
    public function suggest(Request $request)
    {
        // 获取参数
        $search    = $request->input('search');
        
        // 定义对象
        $query = User::query();

        // 查询用户名
        if(!empty($search)) {
            $query->where('users.username','like','%'.$search.'%');
        }

        // 查询数量
        $count = $query
        ->where('users.status', '>', 0)
        ->count();

        // 查询列表
        $lists = $query
        ->limit(20)
        ->where('users.status', '>', 0)
        ->orderBy('id', 'desc')
        ->select('users.username as name','users.id as value')
        ->get()
        ->toArray();

        return $this->success('获取成功！','',$lists);
    }
}
