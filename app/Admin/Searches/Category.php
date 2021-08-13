<?php

namespace App\Admin\Searches;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Searches\Select;

class Category extends Select
{
    /**
     * 初始化
     *
     * @param  string  $type
     * @return void
     */
    public function __construct($type = 'ARTICLE')
    {
        $this->column = 'category_id';
        $this->type = $type;
    }

    /**
     * 分类类型
     *
     * @var string
     */
    public $type = null;

    /**
     * 显示名称
     *
     * @var string
     */
    public $name = '分类';

    /**
     * 执行查询
     *
     * @param  Request  $request
     * @param  Builder  $query
     * @param  mixed  $value
     * @return Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('category_id', $value);
    }

    /**
     * 属性
     *
     * @param  Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return \App\Models\Category::orderedList($this->type);
    }
}