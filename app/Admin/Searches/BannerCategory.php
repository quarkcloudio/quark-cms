<?php

namespace App\Admin\Searches;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Searches\Select;

class BannerCategory extends Select
{
    /**
     * 显示名称
     *
     * @var string
     */
    public $name = '位置';

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
        return \App\Models\BannerCategory::list();
    }
}