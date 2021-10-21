<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class User extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '会员';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'App\User';

    /**
     * 分页
     *
     * @var int|bool
     */
    public static $perPage = 10;

    /**
     * 列表查询
     *
     * @param  Request  $request
     * @return object
     */
    public static function indexQuery(Request $request, $query)
    {
        return $query->orderBy('id','desc');
    }

    /**
     * 字段
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Field::hidden('id','ID')
            ->onlyOnForms(),

            Field::image('avatar','头像', function() {
                $avatar = \json_decode($this->avatar,true);

                return $avatar ? get_picture($avatar['id']) : null;
            })->button('上传头像'),

            Field::text('username','用户名')
            ->rules(['required','min:6','max:20'],['required'=>'用户名必须填写','min'=>'用户名不能少于6个字符','max'=>'用户名不能超过20个字符'])
            ->creationRules(["unique:users"],['unique'=>'用户名已经存在'])
            ->updateRules(["unique:users,username,{id}"],['unique'=>'用户名已经存在']),
    
            Field::text('nickname','昵称')
            ->rules(['required','max:20'],['required'=>'昵称必须填写','max'=>'昵称不能超过20个字符']),
    
            Field::radio('sex','性别')
            ->options(['1' => '男', '2'=> '女'])
            ->default(1),
    
            Field::text('email','邮箱')
            ->rules(['required','email','max:255'],['required'=>'邮箱必须填写','email'=>'邮箱格式错误','max'=>'邮箱不能超过255个字符'])
            ->creationRules(["unique:users"],['unique'=>'邮箱已经存在'])
            ->updateRules(["unique:users,email,{id}"],['unique'=>'邮箱已经存在']),
    
            Field::text('phone','手机号')
            ->rules(['required','max:11'],['required'=>'手机号必须填写','max'=>'手机号不能超过11个字符'])
            ->creationRules(["unique:users"],['unique'=>'手机号已经存在'])
            ->updateRules(["unique:users,phone,{id}"],['unique'=>'手机号已经存在']),

            Field::password('password','密码')
            ->creationRules(['required'], ['required'=>'密码必须填写'])
            ->onlyOnForms(),
            
            Field::datetime('created_at','注册时间')
            ->onlyOnIndex(),

            Field::datetime('last_login_time','最后登录时间')
            ->onlyOnIndex(),

            Field::switch('status','状态')
            ->editable()
            ->trueValue('正常')
            ->falseValue('禁用')
            ->default(true),
        ];
    }

    /**
     * 搜索表单
     *
     * @param  Request  $request
     * @return object
     */
    public function searches(Request $request)
    {
        return [
            new \App\Admin\Searches\Input('username', '用户名'),
            new \App\Admin\Searches\Input('nickname', '昵称'),
            new \App\Admin\Searches\Status,
            new \App\Admin\Searches\DateTimeRange('last_login_time', '登录时间')
        ];
    }

    /**
     * 行为
     *
     * @param  Request  $request
     * @return object
     */
    public function actions(Request $request)
    {
        return [
            (new \App\Admin\Actions\CreateLink($this->title()))->onlyOnIndex(),
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Disable('批量禁用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Enable('批量启用'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\ChangeStatus)->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\EditLink('编辑'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnIndexTableRow(),
            (new \App\Admin\Actions\AccountRecharge)->onlyOnIndexTableRow(),
            new \App\Admin\Actions\FormSubmit,
            new \App\Admin\Actions\FormReset,
            new \App\Admin\Actions\FormBack,
            new \App\Admin\Actions\FormExtraBack
        ];
    }

    /**
     * 保存前回调
     *
     * @param  Request  $request
     * @param  array $data
     * @return object
     */
    public function beforeEditing(Request $request, $data)
    {
        // 编辑的时候，不显示密码
        unset($data['password']);

        return $data;
    }

    /**
     * 保存前回调
     *
     * @param  Request  $request
     * @param  array $submitData
     * @return object
     */
    public function beforeSaving(Request $request, $submitData)
    {
        if(isset($submitData['password'])) {
            $submitData['password'] = bcrypt($submitData['password']);
        }

        return $submitData;
    }
}