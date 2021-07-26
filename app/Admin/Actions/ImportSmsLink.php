<?php

namespace App\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Link;

class ImportSmsLink extends Link
{
    /**
     * 行为名称，当行为在表格行展示时，支持js表达式
     *
     * @var string
     */
    public $name = "发送短信";

    /**
     * 设置按钮类型,primary | ghost | dashed | link | text | default
     *
     * @var string
     */
    public $type = 'primary';

    /**
     * 设置图标
     *
     * @var string
     */
    public $icon = 'plus-circle';

    /**
     * 跳转链接
     *
     * @return string
     */
    public function href()
    {
        return '#/sms/send';
    }
}