<?php

namespace Modules\Mall\Http\Controllers\Admin;

use Illuminate\Http\Request;
use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Modules\Mall\Models\Goods;
use Modules\Mall\Models\GoodsCategory;
use Modules\Mall\Models\GoodsCategoryRelationship;
use Modules\Mall\Models\GoodsType;
use Modules\Mall\Models\GoodsAttribute;
use Modules\Mall\Models\GoodsAttributeValue;
use Modules\Mall\Models\GoodsAttributeAlia;
use Modules\Mall\Models\GoodsCategoryAttribute;
use Modules\Mall\Models\GoodsInfoAttributeValue;
use Modules\Mall\Models\GoodsBrand;
use Modules\Mall\Models\GoodsUnit;
use Modules\Mall\Models\GoodsLayout;
use Modules\Mall\Models\GoodsSku;
use Modules\Mall\Models\GoodsPhoto;
use Modules\Mall\Models\Shop;
use Illuminate\Support\Arr;
use DB;
use Quark;

class GoodsController extends QuarkController
{
    public $title = '商品';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Goods)->title($this->title);
        $grid->column('id','ID');
        $grid->column('cover_id','商品封面')->image();
        $grid->column('goods_name','标题')->link('#/admin/goods/edit');
        $grid->column('tags','商品标签');
        $grid->column('goods_price','售价');
        $grid->column('stock_num','库存');
        $grid->column('created_at','发布时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '销售中'],
            'off' => ['value' => 3, 'text' => '已下架']
        ])->width(100);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('myEdit', '编辑')->link('#/admin/goods/edit');
            $rowAction->menu('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('myCreate', '新增')->type('primary')->link('#/admin/goods/create');
            $action->button('refresh', '刷新');
        });

        // select样式的批量操作
        $grid->batchActions(function($batch) {
            $batch->option('', '批量操作');
            $batch->option('resume', '上架')->model(function($model) {
                $model->update(['status'=>1]);
            });
            $batch->option('forbid', '下架')->model(function($model) {
                $model->update(['status'=>3]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {
            $categorys = GoodsCategory::where('status',1)->get();
            $options[''] = '全部';
            foreach ($categorys as $key => $value) {
                $options[$value['id']] = $value['title'];
            }

            $search->equal('category_id', '商品分类')->select($options)->placeholder('选择分类')->width(110);
            $search->where('goods_name', '搜索内容',function ($query) {
                $query->where('goods_name', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'出售中',2=>'审核中',3=>'已下架',4=>'违规下架'])
            ->placeholder('选择状态')
            ->width(110)
            ->advanced();

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
     * 添加页面
     * 
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request)
    {

        $categorys = GoodsCategory::where('status',1)
        ->select('goods_categories.id','goods_categories.pid','goods_categories.title as label','goods_categories.id as value')
        ->get()
        ->toArray();

        $categoryTrees = list_to_tree($categorys,'id','pid','children',0);

        $shops = Shop::where('status',1)
        ->select('id','title')
        ->get()
        ->toArray();

        $goodsUnits = GoodsUnit::where('status',1)
        ->select('id','name')
        ->get()
        ->toArray();

        $goodsBrands = GoodsBrand::where('status',1)
        ->select('id','name')
        ->get()
        ->toArray();

        $topLayouts = GoodsLayout::where('status',1)
        ->where('position',1)
        ->select('id','layout_name')
        ->get()
        ->toArray();

        $bottomLayouts = GoodsLayout::where('status',1)
        ->where('position',2)
        ->select('id','layout_name')
        ->get()
        ->toArray();

        $packingLayouts = GoodsLayout::where('status',1)
        ->where('position',3)
        ->select('id','layout_name')
        ->get()
        ->toArray();

        $serviceLayouts = GoodsLayout::where('status',1)
        ->where('position',4)
        ->select('id','layout_name')
        ->get()
        ->toArray();

        // 模板数据
        $data['categorys'] = $categoryTrees;
        $data['shops'] = $shops;
        $data['goodsUnits'] = $goodsUnits;
        $data['goodsBrands'] = $goodsBrands;
        $data['topLayouts'] = $topLayouts;
        $data['bottomLayouts'] = $bottomLayouts;
        $data['packingLayouts'] = $packingLayouts;
        $data['serviceLayouts'] = $serviceLayouts;

        return success('获取成功！','',$data);
    }

    /**
     * 根据分类获取商品属性
     * 
     * @param  Request  $request
     * @return Response
     */
    public function attribute(Request $request)
    {
        $categoryId  =   $request->get('categoryId','');
        $shopId      =   $request->get('shopId','');

        $systemGoodsAttributes = GoodsAttribute::join('goods_category_attributes', 'goods_category_attributes.goods_attribute_id', '=', 'goods_attributes.id')
        ->where('goods_category_attributes.goods_category_id',$categoryId)
        ->where('goods_category_attributes.type',1)
        ->orderBy('goods_attributes.sort','asc')
        ->select('goods_attributes.id','goods_attributes.name','goods_attributes.style')
        ->get()
        ->toArray();

        foreach($systemGoodsAttributes as $key => $systemGoodsAttribute)
        {
            $systemGoodsAttributeVnames = GoodsAttributeValue::where('goods_attribute_id',$systemGoodsAttribute['id'])
            ->orderBy('sort','asc')
            ->get()
            ->toArray();

            $systemGoodsAttributes[$key]['vname'] = $systemGoodsAttributeVnames;
        }

        $shopGoodsAttributes = GoodsAttribute::where('type',1)
        ->where('shop_id',$shopId)
        ->orderBy('sort','asc')
        ->get()
        ->toArray();

        foreach($shopGoodsAttributes as $key => $shopGoodsAttribute)
        {
            $shopGoodsAttributeVnames = GoodsAttributeValue::where('goods_attribute_id',$shopGoodsAttribute['id'])
            ->orderBy('sort','asc')
            ->get()
            ->toArray();

            $shopGoodsAttributes[$key]['vname'] = $shopGoodsAttributeVnames;
        }

        $goodsAttributes = GoodsAttribute::join('goods_category_attributes', 'goods_category_attributes.goods_attribute_id', '=', 'goods_attributes.id')
        ->where('goods_category_attributes.type',2)
        ->whereRaw('(goods_category_attributes.goods_category_id = ? or goods_attributes.shop_id = ?)', [$categoryId, $shopId])
        ->orderBy('goods_attributes.sort','asc')
        ->select('goods_attributes.id','goods_attributes.name','goods_attributes.style')
        ->get()
        ->toArray();


        foreach($goodsAttributes as $key => $goodsAttribute)
        {
            $goodsAttributeVnames = GoodsAttributeValue::where('goods_attribute_id',$goodsAttribute['id'])
            ->orderBy('sort','asc')
            ->get()
            ->toArray();

            $goodsAttributes[$key]['vname'] = $goodsAttributeVnames;
        }

        // 模板数据
        $data['systemGoodsAttributes'] = $systemGoodsAttributes;
        $data['shopGoodsAttributes'] = $shopGoodsAttributes;
        $data['goodsAttributes'] = $goodsAttributes;

        return success('获取成功！','',$data);
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

        $shopId                    =   $request->json('shop_id'); // 所属商家
        $goodsCategoryId           =   $request->json('goods_category_id'); // 商品分类
        $goodsMode                 =   $request->json('goods_mode',''); // 商品类别
        $otherCategoryIds          =   $request->json('other_category_ids',0); // 扩展分类
        $goodsName                 =   $request->json('goods_name'); // 商品名称
        $keywords                  =   $request->json('keywords'); // 关键词
        $description               =   $request->json('description',''); // 商品买点
        $pricingMode               =   $request->json('pricing_mode'); // 计价方式
        $goodsUnitId               =   $request->json('goods_unit_id',''); // 商品单位
        $goodsBrandId              =   $request->json('goods_brand_id',''); // 商品品牌
        $goodsShopSpus             =   $request->json('goods_shop_spus'); // 店铺自定义属性名称
        $goodsSkus                 =   $request->json('goods_skus'); // 商品规格
        $goodsMoq                  =   $request->json('goods_moq'); // 最小起订量
        $goodsPrice                =   $request->json('goods_price'); // 店铺价
        $marketPrice               =   $request->json('market_price'); // 市场价
        $costPrice                 =   $request->json('cost_price'); // 成本价
        $stockNum                  =   $request->json('stock_num'); // 库存
        $warnNum                   =   $request->json('warn_num'); // 库存警告数量
        $goodsSn                   =   $request->json('goods_sn'); // 商品货号
        $goodsBarcode              =   $request->json('goods_barcode'); // 商品条形码
        $goodsStockcode            =   $request->json('goods_stockcode'); // 商品库位码
        $coverId                   =   $request->json('cover_id'); // 上传商品默认主图，无规格主图时展示该图
        $videoId                   =   $request->json('video_id'); // 主图视频
        $pcContent                 =   $request->json('pc_content'); // 电脑端商品详情
        $mobileContent             =   $request->json('mobile_content'); // 手机端商品详情
        $topLayoutId               =   $request->json('top_layout_id'); // 详情页顶部模板
        $bottomLayoutId            =   $request->json('bottom_layout_id'); // 详情页底部模板
        $packingLayoutId           =   $request->json('packing_layout_id'); // 详情页包装清单版式
        $serviceLayoutId           =   $request->json('service_layout_id'); // 详情页售后保证版式
        $goodsWeight               =   $request->json('goods_weight'); // 物流重量，商品的重量单位为千克，如果商品的运费模板按照重量计算请填写此项，为空则默认商品重量为0Kg；
        $goodsVolume               =   $request->json('goods_volume'); // 商品的体积单位为立方米，如果商品的运费模板按照体积计算请填写此项，为空则默认商品体积为0立方米；
        $goodsFreightType          =   $request->json('goods_freight_type'); // 运费类型 0：店铺承担运费 ，1：运费模板
        $freightId                 =   $request->json('freight_id'); // 运费模板id
        $effectiveType             =   $request->json('effective_type'); // 当商品为电子卡券类型商品时，兑换生效期类型：1付款完成立即生效，2付款完成N小时后生效,3付款完成次日生效
        $effectiveHour             =   $request->json('effective_hour'); // 当商品为电子卡券类型商品时，兑换生效期类型为付款完成N小时后生效，例如：12，为12小时候生效
        $validPeriodType           =   $request->json('valid_period_type'); // 当商品为电子卡券类型商品时，使用有效期类型：1长期有效，2具有明确截止时间例如2019-01-01到2019-01-31，3自购买之日起，N小时内有效,4自购买之日起，N天内有效
        $addTime                   =   $request->json('add_time');
        $validPeriodHour           =   $request->json('valid_period_hour'); // 当商品为电子卡券类型商品时，使用有效期类型为3自购买之日起，N小时内有效
        $validPeriodDay            =   $request->json('valid_period_day'); // 当商品为电子卡券类型商品时，使用有效期类型为4自购买之日起，N天内有效
        $isExpiredRefund           =   $request->json('is_expired_refund'); // 当商品为电子卡券类型商品时，是否支持过期退款
        $stockMode                 =   $request->json('stock_mode'); // 库存计数：1拍下减库存，2付款减库存，3出库减库存 拍下减库存：买家拍下商品即减少库存，存在恶拍风险。热销商品如需避免超卖可选此方式
        $status                    =   $request->json('status'); // 1出售中，2审核中，3已下架，4商品违规下架
        
        if($addTime) {
            $addTimeBegin              =   $addTime[0]; // 当商品为电子卡券类型商品时，使用有效期类型为2具有明确截止时间时，开始时间
            $addTimeEnd                =   $addTime[1]; // 当商品为电子卡券类型商品时，使用有效期类型为2具有明确截止时间时，结束时间
        } else {
            $addTimeBegin              =   null;
            $addTimeEnd                =   null;
        }

        if(empty($shopId)) {
            return error('请选择所属商家！');
        }

        if(empty($goodsCategoryId)) {
            return error('请选择所属分类！');
        }

        if(empty($goodsName)) {
            return error('请填写商品名称！');
        }

        if(empty($goodsSkus)) {
            if(empty($goodsPrice)) {
                return error('请填写店铺价！');
            }
            if(empty($stockNum)) {
                return error('请填写商品库存！');
            }
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 3;
        }

        $data['goods_name'] = $goodsName;
        $data['shop_id'] = $shopId;
        $data['goods_category_id'] = $goodsCategoryId[count($goodsCategoryId)-1];
        $data['goods_mode'] = $goodsMode;
        $data['keywords'] = $keywords;
        $data['description'] = $description;
        $data['pricing_mode'] = $pricingMode;
        $data['goods_unit_id'] = $goodsUnitId;
        $data['goods_brand_id'] = $goodsBrandId;
        $data['goods_moq'] = $goodsMoq;
        $data['goods_price'] = $goodsPrice;
        $data['market_price'] = $marketPrice;
        $data['cost_price'] = $costPrice;
        $data['stock_num'] = $stockNum;
        $data['warn_num'] = $warnNum;
        $data['goods_sn'] = $goodsSn;
        $data['goods_barcode'] = $goodsBarcode;

        if($coverId) {
            $data['cover_id'] = $coverId[0]['id'];
        }

        if($videoId) {
            $data['video_id'] = $videoId[0]['id'];
        }

        $data['pc_content'] = $pcContent;
        $data['mobile_content'] = $mobileContent;
        $data['top_layout_id'] = $topLayoutId;
        $data['bottom_layout_id'] = $bottomLayoutId;
        $data['packing_layout_id'] = $packingLayoutId;
        $data['service_layout_id'] = $serviceLayoutId;
        $data['goods_weight'] = $goodsWeight;
        $data['goods_volume'] = $goodsVolume;
        $data['goods_freight_type'] = $goodsFreightType;
        $data['freight_id'] = $freightId;
        $data['effective_type'] = $effectiveType;
        $data['effective_hour'] = $effectiveHour;
        $data['valid_period_type'] = $validPeriodType;
        $data['add_time_begin'] = $addTimeBegin;
        $data['add_time_end'] = $addTimeEnd;
        $data['valid_period_hour'] = $validPeriodHour;
        $data['valid_period_day'] = $validPeriodDay;
        $data['is_expired_refund'] = $isExpiredRefund;
        $data['stock_mode'] = $stockMode;
        $data['status'] = $status;

        if(!empty($goodsSkus)) {
            $data['is_sku'] = 1;
        } else {
            $data['is_sku'] = 0;
        }

        // 添加商品sku
        if(!empty($goodsSkus)) {
            foreach($goodsSkus as $key => $value) {

                if(!isset($value['stock_num'])) {
                    return error('请填写库存！');
                }

                if(!isset($value['goods_price'])) {
                    return error('请填写商品价格！');
                }

                if(empty($value['stock_num'])) {
                    return error('请填写库存！');
                }

                if(empty($value['goods_price'])) {
                    return error('请填写商品价格！');
                }
            }
        }

        $result = false;

        // 启动事务
        DB::beginTransaction();
        try {
            $systemAttrs = [];

            foreach($requestData as $key => $value) {
                if(strpos($key,'system_goods_attribute_') !== false) {
                    // 平台系统属性
                    $attrId = str_replace("system_goods_attribute_","",$key);
                    $systemAttrs[] = ['attribute_id' => $attrId,'attribute_value_id' => $value];
                }
            }

            // 平台系统属性
            $data['goods_system_spus'] = json_encode($systemAttrs);

            $shopAttrs = [];

            // "other_attr_name":"产地","other_attr_value":"唐山",
            if(!empty($goodsShopSpus)) {
                $shopAttrs = $goodsShopSpus;
            }

            // 商家自定义属性
            $data['goods_shop_spus'] = json_encode($shopAttrs);

            $result = Goods::create($data);

            if(!empty($otherCategoryIds)) {
                foreach($otherCategoryIds as $otherCategoryIdKey => $otherCategoryId) {
                    $otherCategoryData['goods_id'] = $result->id;
                    $otherCategoryData['goods_category_id'] = $otherCategoryId;
                    GoodsCategoryRelationship::create($otherCategoryData);
                }
            }

            foreach($requestData as $key => $value) {
                if(strpos($key,'system_goods_attribute_') !== false) {
                    // 平台系统属性
                    $attrId = str_replace("system_goods_attribute_","",$key);

                    // 添加平台系统属性spu
                    $data1['goods_id'] = $result->id;
                    $data1['goods_attribute_id'] = $attrId;
                    $data1['goods_attribute_value_id'] = json_encode($value);
                    $data1['type'] = 1;

                    $result1 = GoodsInfoAttributeValue::create($data1);
                }
            }

            // 添加商品sku
            if(!empty($goodsSkus)) {
                $data2['goods_id'] = $result->id;
                $data2['shop_id'] = $shopId;

                foreach($goodsSkus as $key => $value) {

                    $properties = '';
                    $propertyIds = '';
                    $propertyNames = '';

                    ksort($value);

                    foreach($value as $key1 => $value1) {
                        if(strpos($key1,'goodsAttribute_') !== false) {

                            $arr = explode(';',$value1);
                            $goodsAttributeId = explode(':',$arr[0])[1];
                            $goodsAttributeName = explode(':',$arr[1])[1];
                            $goodsAttributeValueId = explode(':',$arr[2])[1];
                            $goodsAttributeValueName = explode(':',$arr[3])[1];

                            $properties = $properties.';'.$goodsAttributeId.':'.$goodsAttributeValueId;
                            $propertyIds = $propertyIds.';'.$goodsAttributeId;
                            $propertyNames = $propertyNames.' '.$goodsAttributeValueName;
                        }
                    }

                    $properties = trim($properties,";");
                    $propertyIds = trim($propertyIds,";");
                    $propertyNames = trim($propertyNames);

                    if(!isset($value['stock_num'])) {
                        return error('请填写库存！');
                    }

                    if(!isset($value['goods_price'])) {
                        return error('请填写商品价格！');
                    }

                    if(!isset($value['cost_price'])) {
                        $value['cost_price'] = 0;
                    }

                    if(!isset($value['market_price'])) {
                        $value['market_price'] = 0;
                    }

                    if(!isset($value['goods_sn'])) {
                        $value['goods_sn'] = '';
                    }

                    if(!isset($value['goods_barcode'])) {
                        $value['goods_barcode'] = '';
                    }

                    if(empty($value['stock_num'])) {
                        return error('请填写库存！');
                    }

                    if(empty($value['goods_price'])) {
                        return error('请填写商品价格！');
                    }

                    $data2['properties'] = $properties;
                    $data2['property_ids'] = $propertyIds;
                    $data2['property_names'] = $propertyNames;
                    $data2['stock_num'] = $value['stock_num'];
                    $data2['cost_price'] = $value['cost_price'];
                    $data2['goods_price'] = $value['goods_price'];
                    $data2['market_price'] = $value['market_price'];
                    $data2['goods_sn'] = $value['goods_sn'];
                    $data2['goods_barcode'] = $value['goods_barcode'];
                    $data2['status'] = $value['status'];

                    $result2 = GoodsSku::create($data2);

                    foreach($value as $key1 => $value1) {
                        if(strpos($key1,'goodsAttribute_') !== false) {

                            $arr = explode(';',$value1);
                            $goodsAttributeId = explode(':',$arr[0])[1];
                            $goodsAttributeName = explode(':',$arr[1])[1];
                            $goodsAttributeValueId = explode(':',$arr[2])[1];
                            $goodsAttributeValueName = explode(':',$arr[3])[1];

                            // 更新或添加规格sku
                            $data3['goods_id'] = $result->id;
                            $data3['goods_sku_id'] = $result2->id;
                            $data3['goods_attribute_id'] = $goodsAttributeId;
                            $data3['goods_attribute_value_id'] = $goodsAttributeValueId;
                            $data3['type'] = 2;
                            GoodsInfoAttributeValue::create($data3);
                        }
                    }
                }
            }

             // 提交事务
            DB::commit();	
        } catch (\Exception $e) {
            // 回滚事务
            DB::rollback();
        }

        if ($result) {
            return success('操作成功！','/goods/imageCreate?id='.$result->id);
        } else {
            return error('操作失败！');
        }
    }

    /**
     * 编辑页面
     *
     * @param  Request  $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');

        if(empty($id)) {
            return error('参数错误！');
        }

        $data = Goods::find($id)->toArray();

        $categorys = GoodsCategory::where('status',1)
        ->select('goods_categories.id','goods_categories.pid','goods_categories.title as label','goods_categories.id as value')
        ->get()
        ->toArray();

        $categoryTrees = list_to_tree($categorys,'id','pid','children',0);

        $shops = Shop::where('status',1)
        ->select('id','title')
        ->get()
        ->toArray();

        $goodsUnits = GoodsUnit::where('status',1)
        ->select('id','name')
        ->get()
        ->toArray();

        $goodsBrands = GoodsBrand::where('status',1)
        ->select('id','name')
        ->get()
        ->toArray();

        $topLayouts = GoodsLayout::where('status',1)
        ->where('position',1)
        ->select('id','layout_name')
        ->get()
        ->toArray();

        $bottomLayouts = GoodsLayout::where('status',1)
        ->where('position',2)
        ->select('id','layout_name')
        ->get()
        ->toArray();

        $packingLayouts = GoodsLayout::where('status',1)
        ->where('position',3)
        ->select('id','layout_name')
        ->get()
        ->toArray();

        $serviceLayouts = GoodsLayout::where('status',1)
        ->where('position',4)
        ->select('id','layout_name')
        ->get()
        ->toArray();


        $categoryId  =   $data['goods_category_id'];
        $shopId      =   $data['shop_id'];

        $systemGoodsAttributes = GoodsAttribute::join('goods_category_attributes', 'goods_category_attributes.goods_attribute_id', '=', 'goods_attributes.id')
        ->where('goods_category_attributes.goods_category_id',$categoryId)
        ->where('goods_category_attributes.type',1)
        ->orderBy('goods_attributes.sort','asc')
        ->select('goods_attributes.id','goods_attributes.name','goods_attributes.style')
        ->get()
        ->toArray();

        foreach($systemGoodsAttributes as $key => $systemGoodsAttribute)
        {
            $systemGoodsAttributeVnames = GoodsAttributeValue::where('goods_attribute_id',$systemGoodsAttribute['id'])
            ->orderBy('sort','asc')
            ->get()
            ->toArray();

            $systemGoodsAttributes[$key]['vname'] = $systemGoodsAttributeVnames;

            $goodsInfoAttributeValue = GoodsInfoAttributeValue::where('goods_id',$data['id'])
            ->where('goods_attribute_id',$systemGoodsAttribute['id'])
            ->where('type',1)
            ->first();

            $systemGoodsAttributes[$key]['goods_attribute_value_id'] = json_decode($goodsInfoAttributeValue['goods_attribute_value_id']);
        }

        $data['goods_shop_spus'] = json_decode($data['goods_shop_spus']);

        $goodsAttributes = GoodsAttribute::join('goods_category_attributes', 'goods_category_attributes.goods_attribute_id', '=', 'goods_attributes.id')
        ->where('goods_category_attributes.type',2)
        ->whereRaw('(goods_category_attributes.goods_category_id = ? or goods_attributes.shop_id = ?)', [$categoryId, $shopId])
        ->orderBy('goods_attributes.sort','asc')
        ->select('goods_attributes.id','goods_attributes.name','goods_attributes.style')
        ->get()
        ->toArray();

        foreach($goodsAttributes as $key => $goodsAttribute)
        {
            $goodsAttributeVnames = GoodsAttributeValue::where('goods_attribute_id',$goodsAttribute['id'])
            ->orderBy('sort','asc')
            ->get()
            ->toArray();

            $goodsAttributes[$key]['vname'] = $goodsAttributeVnames;
        }

        $data['checkedGoodsAttributes'] = GoodsInfoAttributeValue::where('goods_id',$data['id'])
        ->where('type',2)
        ->distinct()
        ->pluck('goods_attribute_id');

        foreach($data['checkedGoodsAttributes'] as $key => $value) {

            $goodsInfoAttributeValues = GoodsInfoAttributeValue::where('goods_attribute_id',$value)
            ->where('goods_id',$data['id'])
            ->where('type',2)
            ->distinct()
            ->pluck('goods_attribute_value_id');
            
            foreach($goodsInfoAttributeValues as $goodsInfoAttributeValueKey => $goodsInfoAttributeValue) {
                $goodsInfoAttributeValues[$goodsInfoAttributeValueKey] = (int)($goodsInfoAttributeValue);
            }

            $getGoodsInfoAttributeData['value'] = $goodsInfoAttributeValues;
            $getGoodsInfoAttributeData['id'] = $value;

            $data['checkedGoodsAttributeValues'][] = $getGoodsInfoAttributeData;
        }
        
        if(count($data['checkedGoodsAttributes'])==0) {
            $data['checkedGoodsAttributes'] = false;
        } 

        //////
        $goodsSkus = GoodsSku::where('goods_id',$data['id'])->get()->toArray();

        foreach($goodsSkus as $goodsSkuKey => $goodsSku) {
            $goodsSkus[$goodsSkuKey]['goods_attributes'] = GoodsInfoAttributeValue::where('goods_id',$data['id'])
            ->where('type',2)
            ->where('goods_sku_id',$goodsSku['id'])
            ->select('goods_attribute_id','goods_attribute_value_id')
            ->get()
            ->toArray();
            $goods_attribute_info = null;
            foreach($goodsSkus[$goodsSkuKey]['goods_attributes'] as $mykey => $myValue) {
                $goods_attribute_info[] = $myValue['goods_attribute_value_id'];
            }

            $goodsSkus[$goodsSkuKey]['goods_attribute_info'] = $goods_attribute_info;
        }

        // 模板数据
        $data['systemGoodsAttributes'] = $systemGoodsAttributes ? $systemGoodsAttributes : false;
        $data['goodsAttributes'] = $goodsAttributes ? $goodsAttributes : false;
        $data['goodsSkus'] = $goodsSkus;

        $getGoodsCategoryId = $this->getParentCategory($data['goods_category_id'],[0 => $data['goods_category_id']]);

        $data['goods_category_id'] = $getGoodsCategoryId;
        $data['other_category_ids'] = GoodsCategoryRelationship::where('goods_id',$id)->pluck('goods_category_id');

        $coverId = $data['cover_id'];
        unset($data['cover_id']);

        $data['cover_id'][0]['id'] =$coverId;
        $data['cover_id'][0]['uid'] =$coverId;
        $data['cover_id'][0]['name'] = get_picture($coverId,'name');
        $data['cover_id'][0]['url'] = get_picture($coverId);

        $videoId = $data['video_id'];
        unset($data['video_id']);

        $data['video_id'][0]['id'] =$videoId;
        $data['video_id'][0]['uid'] =$videoId;
        $data['video_id'][0]['name'] = get_file($videoId,'name');
        $data['video_id'][0]['url'] = get_file($videoId);

        if ($data['is_expired_refund'] == 1) {
            $data['is_expired_refund'] = true;
        } else {
            $data['is_expired_refund'] = false;
        }

        if ($data['status'] == 1) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }

        // 模板数据
        $data['categorys'] = $categoryTrees;
        $data['shops'] = $shops;
        $data['goodsUnits'] = $goodsUnits;
        $data['goodsBrands'] = $goodsBrands;
        $data['topLayouts'] = $topLayouts;
        $data['bottomLayouts'] = $bottomLayouts;
        $data['packingLayouts'] = $packingLayouts;
        $data['serviceLayouts'] = $serviceLayouts;

        return success('获取成功！','',$data);
    }

    protected function getParentCategory($id,$categorys)
    {
        $getCategory = GoodsCategory::where('status',1)
        ->where('id',$id)
        ->first();

        if($getCategory->pid && $getCategory->pid != 0) {
            $categorys = Arr::prepend($categorys, $getCategory->pid);
            return $this->getParentCategory($getCategory->pid,$categorys);
        } else {
            return $categorys;
        }
    }

    /**
     * 保存编辑数据
     *
     * @param  Request  $request
     * @return Response
     */
    public function save(Request $request)
    {
        $id             =   $request->json('id');
        $requestJson    =   $request->getContent();
        $requestData    =   json_decode($requestJson,true);

        $shopId                    =   $request->json('shop_id'); // 所属商家
        $goodsCategoryId           =   $request->json('goods_category_id'); // 商品分类
        $goodsMode                 =   $request->json('goods_mode',''); // 商品类别
        $otherCategoryIds          =   $request->json('other_category_ids',0); // 扩展分类
        $goodsName                 =   $request->json('goods_name'); // 商品名称
        $keywords                  =   $request->json('keywords'); // 关键词
        $description               =   $request->json('description',''); // 商品买点
        $pricingMode               =   $request->json('pricing_mode'); // 计价方式
        $goodsUnitId               =   $request->json('goods_unit_id',''); // 商品单位
        $goodsBrandId              =   $request->json('goods_brand_id',''); // 商品品牌
        $goodsShopSpus             =   $request->json('goods_shop_spus'); // 店铺自定义属性名称
        $goodsSkus                 =   $request->json('goods_skus'); // 商品规格
        $goodsMoq                  =   $request->json('goods_moq'); // 最小起订量
        $goodsPrice                =   $request->json('goods_price'); // 店铺价
        $marketPrice               =   $request->json('market_price'); // 市场价
        $costPrice                 =   $request->json('cost_price'); // 成本价
        $stockNum                  =   $request->json('stock_num'); // 库存
        $warnNum                   =   $request->json('warn_num'); // 库存警告数量
        $goodsSn                   =   $request->json('goods_sn'); // 商品货号
        $goodsBarcode              =   $request->json('goods_barcode'); // 商品条形码
        $goodsStockcode            =   $request->json('goods_stockcode'); // 商品库位码
        $coverId                   =   $request->json('cover_id'); // 上传商品默认主图，无规格主图时展示该图
        $videoId                   =   $request->json('video_id'); // 主图视频
        $pcContent                 =   $request->json('pc_content'); // 电脑端商品详情
        $mobileContent             =   $request->json('mobile_content'); // 手机端商品详情
        $topLayoutId               =   $request->json('top_layout_id'); // 详情页顶部模板
        $bottomLayoutId            =   $request->json('bottom_layout_id'); // 详情页底部模板
        $packingLayoutId           =   $request->json('packing_layout_id'); // 详情页包装清单版式
        $serviceLayoutId           =   $request->json('service_layout_id'); // 详情页售后保证版式
        $goodsWeight               =   $request->json('goods_weight'); // 物流重量，商品的重量单位为千克，如果商品的运费模板按照重量计算请填写此项，为空则默认商品重量为0Kg；
        $goodsVolume               =   $request->json('goods_volume'); // 商品的体积单位为立方米，如果商品的运费模板按照体积计算请填写此项，为空则默认商品体积为0立方米；
        $goodsFreightType          =   $request->json('goods_freight_type'); // 运费类型 0：店铺承担运费 ，1：运费模板
        $freightId                 =   $request->json('freight_id'); // 运费模板id
        $effectiveType             =   $request->json('effective_type'); // 当商品为电子卡券类型商品时，兑换生效期类型：1付款完成立即生效，2付款完成N小时后生效,3付款完成次日生效
        $effectiveHour             =   $request->json('effective_hour'); // 当商品为电子卡券类型商品时，兑换生效期类型为付款完成N小时后生效，例如：12，为12小时候生效
        $validPeriodType           =   $request->json('valid_period_type'); // 当商品为电子卡券类型商品时，使用有效期类型：1长期有效，2具有明确截止时间例如2019-01-01到2019-01-31，3自购买之日起，N小时内有效,4自购买之日起，N天内有效
        $addTime                   =   $request->json('add_time');
        $validPeriodHour           =   $request->json('valid_period_hour'); // 当商品为电子卡券类型商品时，使用有效期类型为3自购买之日起，N小时内有效
        $validPeriodDay            =   $request->json('valid_period_day'); // 当商品为电子卡券类型商品时，使用有效期类型为4自购买之日起，N天内有效
        $isExpiredRefund           =   $request->json('is_expired_refund'); // 当商品为电子卡券类型商品时，是否支持过期退款
        $stockMode                 =   $request->json('stock_mode'); // 库存计数：1拍下减库存，2付款减库存，3出库减库存 拍下减库存：买家拍下商品即减少库存，存在恶拍风险。热销商品如需避免超卖可选此方式
        $status                    =   $request->json('status'); // 1出售中，2审核中，3已下架，4商品违规下架
        
        if($addTime) {
            $addTimeBegin              =   $addTime[0]; // 当商品为电子卡券类型商品时，使用有效期类型为2具有明确截止时间时，开始时间
            $addTimeEnd                =   $addTime[1]; // 当商品为电子卡券类型商品时，使用有效期类型为2具有明确截止时间时，结束时间
        } else {
            $addTimeBegin              =   null;
            $addTimeEnd                =   null;
        }

        if(empty($shopId)) {
            return error('请选择所属商家！');
        }

        if(empty($goodsCategoryId)) {
            return error('请选择所属分类！');
        }

        if(empty($goodsName)) {
            return error('请填写商品名称！');
        }

        if(empty($goodsSkus)) {
            if(empty($goodsPrice)) {
                return error('请填写店铺价！');
            }
            if(empty($stockNum)) {
                return error('请填写商品库存！');
            }
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 3;
        }

        $data['goods_name'] = $goodsName;
        $data['shop_id'] = $shopId;
        $data['goods_category_id'] = $goodsCategoryId[count($goodsCategoryId)-1];
        $data['goods_mode'] = $goodsMode;
        $data['keywords'] = $keywords;
        $data['description'] = $description;
        $data['pricing_mode'] = $pricingMode;
        $data['goods_unit_id'] = $goodsUnitId;
        $data['goods_brand_id'] = $goodsBrandId;
        $data['goods_moq'] = $goodsMoq;
        $data['goods_price'] = $goodsPrice;
        $data['market_price'] = $marketPrice;
        $data['cost_price'] = $costPrice;
        $data['stock_num'] = $stockNum;
        $data['warn_num'] = $warnNum;
        $data['goods_sn'] = $goodsSn;
        $data['goods_barcode'] = $goodsBarcode;

        if($coverId) {
            $data['cover_id'] = $coverId[0]['id'];
        }

        if($videoId) {
            $data['video_id'] = $videoId[0]['id'];
        }

        $data['pc_content'] = $pcContent;
        $data['mobile_content'] = $mobileContent;
        $data['top_layout_id'] = $topLayoutId;
        $data['bottom_layout_id'] = $bottomLayoutId;
        $data['packing_layout_id'] = $packingLayoutId;
        $data['service_layout_id'] = $serviceLayoutId;
        $data['goods_weight'] = $goodsWeight;
        $data['goods_volume'] = $goodsVolume;
        $data['goods_freight_type'] = $goodsFreightType;
        $data['freight_id'] = $freightId;
        $data['effective_type'] = $effectiveType;
        $data['effective_hour'] = $effectiveHour;
        $data['valid_period_type'] = $validPeriodType;
        $data['add_time_begin'] = $addTimeBegin;
        $data['add_time_end'] = $addTimeEnd;
        $data['valid_period_hour'] = $validPeriodHour;
        $data['valid_period_day'] = $validPeriodDay;
        $data['is_expired_refund'] = $isExpiredRefund;
        $data['stock_mode'] = $stockMode;
        $data['status'] = $status;

        if(!empty($goodsSkus)) {
            $data['is_sku'] = 1;
        } else {
            $data['is_sku'] = 0;
        }

        // 添加商品sku
        if(!empty($goodsSkus)) {
            foreach($goodsSkus as $key => $value) {

                if(!isset($value['stock_num'])) {
                    return error('请填写库存！');
                }

                if(!isset($value['goods_price'])) {
                    return error('请填写商品价格！');
                }

                if(empty($value['stock_num'])) {
                    return error('请填写库存！');
                }

                if(empty($value['goods_price'])) {
                    return error('请填写商品价格！');
                }
            }
        }

        $result = false;

        // 启动事务
        DB::beginTransaction();
        try {
            $systemAttrs = [];

            foreach($requestData as $key => $value) {
                if(strpos($key,'system_goods_attribute_') !== false) {
                    // 平台系统属性
                    $attrId = str_replace("system_goods_attribute_","",$key);
                    $systemAttrs[] = ['attribute_id' => $attrId,'attribute_value_id' => $value];
                }
            }

            // 平台系统属性
            $data['goods_system_spus'] = json_encode($systemAttrs);

            $shopAttrs = [];

            // "other_attr_name":"产地","other_attr_value":"唐山",
            if(!empty($goodsShopSpus)) {
                $shopAttrs = $goodsShopSpus;
            }

            // 商家自定义属性
            $data['goods_shop_spus'] = json_encode($shopAttrs);

            $result = Goods::where('id',$id)->update($data);

            GoodsCategoryRelationship::where('goods_id',$id)->delete();

            if(!empty($otherCategoryIds)) {
                foreach($otherCategoryIds as $otherCategoryIdKey => $otherCategoryId) {
                    $otherCategoryData['goods_id'] = $id;
                    $otherCategoryData['goods_category_id'] = $otherCategoryId;
                    GoodsCategoryRelationship::create($otherCategoryData);
                }
            }

            GoodsInfoAttributeValue::where('goods_id',$id)->delete();

            foreach($requestData as $key => $value) {
                if(strpos($key,'system_goods_attribute_') !== false) {
                    // 平台系统属性
                    $attrId = str_replace("system_goods_attribute_","",$key);

                    // 添加平台系统属性spu
                    $data1['goods_id'] = $id;
                    $data1['goods_attribute_id'] = $attrId;
                    $data1['goods_attribute_value_id'] = json_encode($value);
                    $data1['type'] = 1;

                    $result1 = GoodsInfoAttributeValue::create($data1);
                }
            }

            // 添加商品sku
            if(!empty($goodsSkus)) {
                $data2['goods_id'] = $id;
                $data2['shop_id'] = $shopId;

                foreach($goodsSkus as $key => $value) {

                    $properties = '';
                    $propertyIds = '';
                    $propertyNames = '';

                    ksort($value);

                    foreach($value as $key1 => $value1) {
                        if(strpos($key1,'goodsAttribute_') !== false) {

                            $arr = explode(';',$value1);
                            $goodsAttributeId = explode(':',$arr[0])[1];
                            $goodsAttributeName = explode(':',$arr[1])[1];
                            $goodsAttributeValueId = explode(':',$arr[2])[1];
                            $goodsAttributeValueName = explode(':',$arr[3])[1];

                            $properties = $properties.';'.$goodsAttributeId.':'.$goodsAttributeValueId;
                            $propertyIds = $propertyIds.';'.$goodsAttributeId;
                            $propertyNames = $propertyNames.' '.$goodsAttributeValueName;
                        }
                    }

                    $properties = trim($properties,";");
                    $propertyIds = trim($propertyIds,";");
                    $propertyNames = trim($propertyNames);

                    $dontNeedDelSku = GoodsSku::where('property_ids',$propertyIds)->where('goods_id',$id)->first();
                    if(!$dontNeedDelSku) {
                        GoodsSku::where('goods_id',$id)->delete();
                    }

                    if(!isset($value['stock_num'])) {
                        return error('请填写库存！');
                    }

                    if(!isset($value['goods_price'])) {
                        return error('请填写商品价格！');
                    }

                    if(!isset($value['cost_price'])) {
                        $value['cost_price'] = 0;
                    }

                    if(!isset($value['market_price'])) {
                        $value['market_price'] = 0;
                    }

                    if(!isset($value['goods_sn'])) {
                        $value['goods_sn'] = '';
                    }

                    if(!isset($value['goods_barcode'])) {
                        $value['goods_barcode'] = '';
                    }

                    if(empty($value['stock_num'])) {
                        return error('请填写库存！');
                    }

                    if(empty($value['goods_price'])) {
                        return error('请填写商品价格！');
                    }

                    $data2['properties'] = $properties;
                    $data2['property_ids'] = $propertyIds;
                    $data2['property_names'] = $propertyNames;
                    $data2['stock_num'] = $value['stock_num'];
                    $data2['cost_price'] = $value['cost_price'];
                    $data2['goods_price'] = $value['goods_price'];
                    $data2['market_price'] = $value['market_price'];
                    $data2['goods_sn'] = $value['goods_sn'];
                    $data2['goods_barcode'] = $value['goods_barcode'];
                    $data2['status'] = $value['status'];

                    $hasGoodsSku = GoodsSku::where('properties',$properties)->where('goods_id',$id)->first();
                    if($hasGoodsSku) {
                        $result2 = GoodsSku::where('id',$hasGoodsSku['id'])->update($data2);
                        $goodsSkuId = $hasGoodsSku['id'];
                    } else {
                        $result2 = GoodsSku::create($data2);
                        $goodsSkuId = $result2->id;
                    }

                    foreach($value as $key1 => $value1) {
                        if(strpos($key1,'goodsAttribute_') !== false) {

                            $arr = explode(';',$value1);
                            $goodsAttributeId = explode(':',$arr[0])[1];
                            $goodsAttributeName = explode(':',$arr[1])[1];
                            $goodsAttributeValueId = explode(':',$arr[2])[1];
                            $goodsAttributeValueName = explode(':',$arr[3])[1];

                            // 更新或添加规格sku
                            $data3['goods_id'] = $id;
                            $data3['goods_sku_id'] = $goodsSkuId;
                            $data3['goods_attribute_id'] = $goodsAttributeId;
                            $data3['goods_attribute_value_id'] = $goodsAttributeValueId;
                            $data3['type'] = 2;
                            GoodsInfoAttributeValue::create($data3);
                        }
                    }
                }
            }

            // 提交事务
            DB::commit();
        } catch (\Exception $e) {
            // 回滚事务
            DB::rollback();
        }

        if ($result) {
            return success('操作成功！');
        } else {
            return error('操作失败！');
        }
    }

    /**
     * 上传封面图
     * 
     * @param  Request  $request
     * @return Response
     */
    public function imageStore(Request $request)
    {
        $goodsId     =   $request->json('goods_id'); // 商品ID
        $fileList    =   $request->json('file_list'); // 封面图
 
        GoodsPhoto::where('goods_id',$goodsId)->delete();

        if(!empty($fileList)) {
            foreach($fileList as $key => $value) {
                $data['goods_id'] = $goodsId;
                $data['goods_sku_id'] = null;
                $data['cover_id'] = $value['id'];
                $data['sort'] = $key;
                GoodsPhoto::create($data);
            }
        }

        return success('操作成功！','/goods/complete?id='.$goodsId);
    }

    /**
     * 编辑上传封面图
     * 
     * @param  Request  $request
     * @return Response
     */
    public function imageEdit(Request $request)
    {
        $id = $request->get('id');

        if(empty($id)) {
            return error('参数错误！');
        }

        $goodsPhotos = GoodsPhoto::where('goods_id',$id)
        ->get()
        ->toArray();

        $data['cover_id'] = false;

        if(!empty($goodsPhotos)) {
            foreach($goodsPhotos as $key => $value) {
                $data['cover_id'][$key]['id'] =$value['cover_id'];
                $data['cover_id'][$key]['uid'] =$value['cover_id'];
                $data['cover_id'][$key]['name'] = get_picture($value['cover_id'],'name');
                $data['cover_id'][$key]['url'] = get_picture($value['cover_id']);
            }
        }

        return success('操作成功！','',$data);
    }

    /**
     * 编辑上传封面图
     * 
     * @param  Request  $request
     * @return Response
     */
    public function imageSave(Request $request)
    {
        $goodsId     =   $request->json('goods_id'); // 商品ID
        $fileList    =   $request->json('file_list'); // 封面图

        GoodsPhoto::where('goods_id',$goodsId)->delete();

        if(!empty($fileList)) {
            foreach($fileList as $key => $value) {
                $data['goods_id'] = $goodsId;
                $data['goods_sku_id'] = null;
                $data['cover_id'] = $value['id'];
                $data['sort'] = $key;
                GoodsPhoto::create($data);
            }
        }

        return success('操作成功！');
    }

    /**
     * 发布商品完成
     * 
     * @param  Request  $request
     * @return Response
     */
    public function complete(Request $request)
    {
        $goodsId = $request->get('id'); // 商品ID

        $result['viewGoodsUrl'] = url('goods/detail?id='.$goodsId);
        $result['createGoodsUrl'] = '#/admin/goods/create';
        $result['goodsIndexUrl'] = '#/quark/engine?api=admin/goods/index&component=table';

        if ($result) {
            return success('操作成功！','',$result);
        } else {
            return error('操作失败！');
        }
    }
}
