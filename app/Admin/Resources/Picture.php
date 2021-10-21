<?php

namespace App\Admin\Resources;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Field;
use QuarkCMS\QuarkAdmin\Resource;

class Picture extends Resource
{
    /**
     * 页面标题
     *
     * @var string
     */
    public static $title = '图片';

    /**
     * 模型
     *
     * @var string
     */
    public static $model = 'QuarkCMS\QuarkAdmin\Models\Picture';

    /**
     * 分页
     *
     * @var int|bool
     */
    public static $perPage = 10;

    /**
     * 定义排序
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
            Field::text('path','显示', function() {
                return "<img src='" . get_picture($this->id) . "' width=50 height=50 />";
            })->onlyOnIndex(),
            Field::text('name','名称')->onlyOnIndex(),
            Field::text('size','大小')->onlyOnIndex(),
            Field::text('width','宽度')->onlyOnIndex(),
            Field::text('height','高度')->onlyOnIndex(),
            Field::text('ext','扩展名')->onlyOnIndex(),
            Field::datetime('created_at','上传时间')->onlyOnIndex(),
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
            new \App\Admin\Searches\Input('name', '名称'),
            new \App\Admin\Searches\DateTimeRange('created_at', '上传时间')
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
            (new \App\Admin\Actions\Delete('批量删除'))->onlyOnIndexTableAlert(),
            (new \App\Admin\Actions\Delete('删除'))->onlyOnIndexTableRow(),
        ];
    }
}