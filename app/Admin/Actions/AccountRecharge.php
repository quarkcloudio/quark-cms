<?php

namespace App\Admin\Actions;

use QuarkCMS\Quark\Facades\Form;
use QuarkCMS\QuarkAdmin\Actions\Modal;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\Quark\Facades\Action;
use QuarkCMS\Quark\Facades\Tpl;
use QuarkCMS\Quark\Facades\Space;
use Illuminate\Support\Facades\DB;

class AccountRecharge extends Modal
{
    /**
     * 行为名称，当行为在表格行展示时，支持js表达式
     *
     * @var string
     */
    public $name = '充值';

    /**
     * 设置按钮类型,primary | ghost | dashed | link | text | default
     *
     * @var string
     */
    public $type = 'link';

    /**
     * 设置按钮大小,large | middle | small | default
     *
     * @var string
     */
    public $size = 'small';

    /**
     * 执行成功后刷新的组件
     *
     * @var string
     */
    public $reload = 'table';

    /**
     * 接口接收的参数
     *
     * @return string
     */
    public function apiParams()
    {
        return ['id'];
    }

    /**
     * 执行行为
     *
     * @param  Fields  $fields
     * @param  Collection  $model
     * @return mixed
     */
    public function handle($fields, $model)
    {
        if (empty($fields->remark)) {
            return error('理由必需填写！');
        }

        $result = false;

        DB::beginTransaction();
        try {
            if (!empty($fields->money)) {
                $model->increment('money', $fields->money);
            }

            if (!empty($fields->score)) {
                $model->increment('score', $fields->score);
            }
            
            $result = true;

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
        }

        return $result ? success('操作成功！','reload') : error('操作失败！');
    }

    /**
     * 内容
     * 
     * @return $string
     */
    public function body()
    {
        return Form::key('rechargeModalForm')
        ->api($this->api())
        ->items($this->fields())
        ->labelCol([
            'span' => 6
        ])
        ->wrapperCol([
            'span' => 18
        ]);
    }

    /**
     * 充值字段
     *
     * @return array
     */
    public function fields()
    {
        return [
            Space::body(
                [
                    Tpl::body('充值用户: ${username}（${nickname}）')
                    ->style(['marginLeft'=>'50px']),
        
                    Tpl::body('当前余额: ${money}')
                    ->style(['marginLeft'=>'50px']),
        
                    Tpl::body('当前积分: ${score}')
                    ->style(['marginLeft'=>'50px']),
                ]
            )
            ->direction('vertical')
            ->size('middle')
            ->style(['marginBottom'=>'20px']),

            Field::number('money','余额充值')
            ->extra('正数为充值，负数为扣款')
            ->value(0),

            Field::number('score','积分充值')
            ->extra('正数为充值，负数为扣除')
            ->value(0),

            Field::textArea('remark','充值理由')
            ->rules(['required','max:190'],['required'=>'充值理由必须填写','max'=>'充值理由不能超过190个字符'])
        ];
    }

    /**
     * 弹窗行为
     *
     * @return $this
     */
    public function actions()
    {
        return [
            Action::make('取消')->actionType('cancel'),

            Action::make("提交")
            ->reload('table')
            ->type('primary')
            ->actionType('submit')
            ->submitForm('rechargeModalForm')
        ];
    }
}