<?php

namespace Modules\Mall\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use Modules\Mall\Models\GoodsOrderAftersale;
use Modules\Mall\Models\GoodsOrderAftersaleRecord;
use DB;
use Quark;

class GoodsAfterSaleController extends QuarkController
{
    public $title = '售后订单';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new GoodsOrderAftersale)->title($this->title);
        $grid->column('id','ID');
        $grid->column('goods.goods_name','商品名称')->link('#/admin/goodsAfterSale/info');
        $grid->column('user.username','买家');
        $grid->column('goodsOrderDetail.total_amount','交易金额');
        $grid->column('refund_amount','退款金额');
        $grid->column('created_at','申请时间');
        $grid->column('status','售后状态');

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('myEdit', '处理申请')->link('#/admin/goodsAttribute/info');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('refresh', '刷新');
        });

        $grid->search(function($search) {

            $search->where('name', '搜索内容',function ($query) {
                $query->where('name', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',0=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110)
            ->advanced();

        })->expand(false);

        $grid->model()
        ->paginate(10);

        return $grid;
    }
}
