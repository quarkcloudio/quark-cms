<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Quark;
use QuarkCMS\QuarkAdmin\Http\Controllers\Controller;

class UserController extends Controller
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
        $table = Quark::table(new User)->title($this->title);
        $table->column('id','ID');
        $table->column('avatar','头像')->image();
        $table->column('username','用户名')->editLink();
        $table->column('nickname','昵称');
        $table->column('phone','手机号');
        $table->column('email','邮箱');
        $table->column('created_at','注册时间');
        $table->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 0, 'text' => '禁用']
        ])->width(100);

        $table->column('actions','操作')->width(260)->actions(function($action,$row) {

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

            // 下拉菜单形式的行为
            $action->dropdown('更多')->overlay(function($action) use($row) {
                $action->item('详情')->showLink();
                $action->item('充值')->modalForm(backend_url('api/admin/user/recharge?id='.$row['id']));
            });
        });

        $table->toolBar()->actions(function($action) {
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

        $table->search(function($search) {

            $search->where('usernameOrNickname', '搜索内容',function ($query) {
                $query->where('username', 'like', "%{input}%")->orWhere('nickname', 'like', "%{input}%")->orWhere('phone', 'like', "%{input}%");
            })->placeholder('用户名/手机号/昵称');

            $search->equal('status', '所选状态')->select([''=>'全部',1=>'正常',0=>'已禁用'])->placeholder('选择状态');
            
            $search->between('created_at', '注册时间')->datetime();
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
        $form = Quark::form(new User);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        $form->hidden('id');

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

        // 编辑页面展示前回调
        $form->editing(function ($form) {
            if(isset($form->initialValues['avatar'])) {
                $form->initialValues['avatar'] = get_picture($form->initialValues['avatar'],0,'all');
            }
        });

        // 保存数据前回调
        $form->saving(function ($form) {
            if(isset($form->data['password'])) {
                $form->data['password'] = bcrypt($form->data['password']);
            }

            if(isset($form->data['avatar'])) {
                $form->data['avatar'] = $form->data['avatar']['id'];
            }
        });

        // 保存数据后回调
        $form->saved(function ($form) {
            if($form->model()) {
                return success('操作成功！',frontend_url('admin/user/index'));
            } else {
                return error('操作失败，请重试！');
            }
        });

        return $form;
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
            $id        =   $request->input('id');
            $money     =   $request->input('money',0);
            $score     =   $request->input('score',0);
            $remark    =   $request->input('remark');
    
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
            $form->labelCol(['span' => 4])->title('账户充值')->api('admin/user/recharge');
            $form->hidden('id')->value($id);
            $form->display('充值用户')->value($user['username'].'（'.$user['nickname'].'）');
            $form->display('当前余额')->style(['color'=>'#f81d22'])->value($user['money']);
            $form->display('当前积分')->style(['color'=>'#f81d22'])->value($user['score']);
            $form->number('money','余额充值')->extra('正数为充值，负数为扣款')->value(0);
            $form->number('score','积分充值')->extra('正数为充值，负数为扣除')->value(0);
            $form->textArea('remark','充值理由')
            ->rules(['required','max:190'],['required'=>'充值理由必须填写','max'=>'充值理由不能超过190个字符']);

            return success('获取成功！','',$form);
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
            $query->where('username','like','%'.$search.'%');
        }

        // 查询列表
        $users = $query
        ->limit(20)
        ->where('status', '>', 0)
        ->orderBy('id', 'desc')
        ->select('username as label','id as value')
        ->get()
        ->toArray();

        return success('获取成功！','',$users);
    }
}
