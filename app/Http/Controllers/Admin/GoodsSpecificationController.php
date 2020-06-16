<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Helper;
use App\Builder\Forms\Controls\ID;
use App\Builder\Forms\Controls\Input;
use App\Builder\Forms\Controls\Text;
use App\Builder\Forms\Controls\TextArea;
use App\Builder\Forms\Controls\InputNumber;
use App\Builder\Forms\Controls\Checkbox;
use App\Builder\Forms\Controls\Radio;
use App\Builder\Forms\Controls\Select;
use App\Builder\Forms\Controls\SwitchButton;
use App\Builder\Forms\Controls\DatePicker;
use App\Builder\Forms\Controls\RangePicker;
use App\Builder\Forms\Controls\Editor;
use App\Builder\Forms\Controls\Image;
use App\Builder\Forms\Controls\File;
use App\Builder\Forms\Controls\Button;
use App\Builder\Forms\Controls\Popconfirm;
use App\Builder\Forms\Controls\Area;
use App\Builder\Forms\Controls\Map;
use App\Builder\Forms\Controls\SearchInput;
use App\Builder\Forms\FormBuilder;
use App\Builder\Lists\Tables\Table;
use App\Builder\Lists\Tables\Column;
use App\Builder\Lists\ListBuilder;
use App\Builder\Tabs;
use App\Builder\TabPane;
use App\Models\Goods;
use App\Models\GoodsCategory;
use App\Models\GoodsCategoryRelationship;
use App\Models\GoodsType;
use App\Models\GoodsAttribute;
use App\Models\GoodsAttributeValue;
use App\Models\GoodsAttributeAlia;
use App\Models\GoodsCategoryAttribute;
use App\Models\GoodsInfoAttributeValue;
use App\Models\GoodsBrand;
use App\Models\GoodsUnit;
use App\Models\GoodsLayout;
use App\Models\GoodsSku;
use App\Models\GoodsPhoto;
use App\Models\Shop;
use DB;

class GoodsSpecificationController extends BuilderController
{
    public function __construct()
    {
        $this->pageTitle = '商品';
    }

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    public function specificationIndex(Request $request)
    {
        $this->pageTitle = '商品规格';
        // 获取参数
        $current            = intval($request->get('current',1));
        $pageSize           = intval($request->get('pageSize',10));
        $search             = $request->get('search');
        $specificationSelectedIds     = $request->get('specificationSelectedIds');
            
        // 定义对象
        $query = GoodsAttribute::query();

        // 查询
        if(!empty($search)) {

            // 名称
            if(isset($search['name'])) {
                if(!empty($search['name'])) {
                    $query->where('goods_attributes.name',$search['name']);
                }
            }

            // 类型
            if(isset($search['goodsTypeId'])) {
                if(!empty($search['goodsTypeId'])) {
                    $query->where('goods_attributes.goods_type_id',$search['goodsTypeId']);
                }
            }

            // 状态
            if(isset($search['status']) && $search['status'] !=0) {
                if(!empty($search['status'])) {
                    $query->where('goods_attributes.status',$search['status']);
                }
            }
        }

        if(isset($specificationSelectedIds)) {
            $query->whereNotIn('goods_attributes.id', $specificationSelectedIds);
        }

        // 查询数量
        $total = $query
        ->where('goods_attributes.status', '>', 0)
        ->where('goods_attributes.type', 2)
        ->count();

        // 查询列表
        $lists = $query
        ->join('goods_types', 'goods_attributes.goods_type_id', '=', 'goods_types.id')
        ->skip(($current-1)*$pageSize)
        ->take($pageSize)
        ->where('goods_attributes.status', '>', 0)
        ->where('goods_attributes.type', 2)
        ->orderBy('goods_attributes.id', 'desc')
        ->select('goods_attributes.*','goods_types.name as goods_type_name')
        ->get()
        ->toArray();

        // 默认页码
        $pagination['defaultCurrent'] = 1;
        // 当前页码
        $pagination['current'] = $current;
        // 分页数量
        $pagination['pageSize'] = $pageSize;
        // 总数量
        $pagination['total'] = $total;

        foreach ($lists as $key => $list) {

            if($list['status'] == 1) {
                $lists[$key]['status'] = '正常';
            }

            if($list['status'] == 2) {
                $lists[$key]['status'] = '已禁用';
            }

            switch ($list['style']) {
                case 1:
                    $lists[$key]['style'] = '多选';
                    break;
                case 2:
                    $lists[$key]['style'] = '单选';
                    break;
                case 3:
                    $lists[$key]['style'] = '文本';
                    break;
                default:
                    $lists[$key]['style'] = '未知';
            }

            $goodsAttributeValues = GoodsAttributeValue::where('goods_attribute_id',$list['id'])->pluck('vname')->toArray();

            $lists[$key]['goods_attribute_values'] = implode(',',$goodsAttributeValues);
        }

        $status = [
            [
                'name'=>'所有状态',
                'value'=>'0',
            ],
            [
                'name'=>'正常',
                'value'=>'1',
            ],
            [
                'name'=>'禁用',
                'value'=>'2',
            ],
        ];

        $goodsTypes[0]['name'] = '请选择商品类型';
        $goodsTypes[0]['value'] = '0';

        $getGoodsTypes = GoodsType::where('status',1)->select('name','id as value')->get();

        foreach ($getGoodsTypes as $key => $getGoodsType) {
            $goodsTypes[$key+1]['name'] = $getGoodsType['name'];
            $goodsTypes[$key+1]['value'] = $getGoodsType['value'];
        }

        $searchs = [
            Select::make('商品类型','goodsTypeId')->option($goodsTypes)->value('0'),
            Select::make('状态','status')->option($status)->value('0'),
            Input::make('搜索内容','name'),
            Button::make('搜索')->onClick('search'),
        ];

        $columns = [
            Column::make('ID','id'),
            Column::make('规格名称','name')->withA('admin/mall/'.$this->controllerName().'/specificationEdit'),
            Column::make('规格类型','goods_type_name'),
            Column::make('样式','style'),
            Column::make('规格值','goods_attribute_values'),
            Column::make('排序','sort'),
            Column::make('状态','status')->withTag("text === '已禁用' ? 'red' : 'blue'"),
        ];

        $headerButtons = [
            Button::make('新增'.$this->pageTitle)->icon('plus-circle')->type('primary')->href('admin/mall/goods/specificationCreate'),
        ];

        $toolbarButtons = [
            Button::make('启用')->type('primary')->onClick('multiChangeStatus','1','admin/'.$this->controllerName().'/specificationChangeStatus'),
            Button::make('禁用')->onClick('multiChangeStatus','2','admin/'.$this->controllerName().'/specificationChangeStatus'),
            Button::make('删除')->type('danger')->onClick('multiChangeStatus','-1','admin/'.$this->controllerName().'/specificationChangeStatus'),
        ];

        $actions = [
            Button::make('启用|禁用')->type('link')->onClick('changeStatus','1|2','admin/'.$this->controllerName().'/specificationChangeStatus')->style(['paddingLeft'=>5,'paddingRight'=>5]),
            Button::make('编辑')->type('link')->href('admin/mall/goods/specificationEdit')->style(['paddingLeft'=>5,'paddingRight'=>5]),
            Popconfirm::make('删除')->type('link')->title('确定删除吗？')->onConfirm('changeStatus','-1','admin/'.$this->controllerName().'/specificationChangeStatus')->style(['paddingLeft'=>5,'paddingRight'=>5]),
        ];

        $data = $this->listBuilder($columns,$lists,$pagination,$searchs,[],$headerButtons,null,$actions);

        return $this->success('获取成功！','',$data);
    }

    /**
     * 添加页面
     * 
     * @param  Request  $request
     * @return Response
     */
    public function specificationCreate(Request $request)
    {
        $id   =   $request->json('id');

        $data['goods_types'] = GoodsType::where('status',1)->get();

        if(!empty($data)) {
            return $this->success('获取成功！','',$data);
        } else {
            return $this->success('获取失败！');
        }
    }

    /**
     * 保存方法
     * 
     * @param  Request  $request
     * @return Response
     */
    public function specificationStore(Request $request)
    {
        $goodsTypeId            =   $request->json('goods_type_id');
        $name                   =   $request->json('name');
        $description            =   $request->json('description');
        $style                  =   $request->json('style');
        $attributeValues        =   $request->json('attribute_values');
        $attributeValuesSort    =   $request->json('attribute_values_sort');
        $sort                   =   $request->json('sort');
        $status                 =   $request->json('status');
        
        if (empty($goodsTypeId)) {
            return $this->error('请选择商品类型！');
        }

        if (empty($name)) {
            return $this->error('属性名称必须填写！');
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 2; //禁用
        }

        $data['goods_type_id']  = $goodsTypeId;
        $data['name']           = $name;
        $data['description']    = $description;
        $data['sort']           = $sort;
        $data['status']         = $status;
        $data['style']          = $style;
        $data['type']           = 2;

        $result = GoodsAttribute::create($data);

        if(!empty($result) && !empty($attributeValues)) {
            foreach($attributeValues as $key => $attributeValue) {
                $data1['goods_attribute_id'] = $result->id;
                $data1['vname'] = $attributeValue;
                $data1['sort'] = $attributeValuesSort[$key];
                $result1 = GoodsAttributeValue::create($data1);
            }
        }

        if($result) {
            return $this->success('操作成功！','specificationIndex');
        } else {
            return $this->error('操作失败！');
        }
    }

    /**
     * 编辑页面
     *
     * @param  Request  $request
     * @return Response
     */
    public function specificationEdit(Request $request)
    {
        $id = $request->get('id');

        if(empty($id)) {
            return $this->error('参数错误！');
        }

        $goodsAttribute = GoodsAttribute::find($id)->toArray();

        $goodsAttributeValues = GoodsAttributeValue::where('goods_attribute_id',$goodsAttribute['id'])->get();

        $data['goods_attribute'] = $goodsAttribute;
        $data['goods_attribute_values'] = $goodsAttributeValues;
        $data['goods_types'] = GoodsType::where('status',1)->get();

        $data['keys'] = [];
        foreach($goodsAttributeValues as $key => $value) {
            $data['keys'][] = $key;
        }
        
        if(!empty($data)) {
            return $this->success('操作成功！','',$data);
        } else {
            return $this->error('操作失败！');
        }
    }

    /**
     * 保存编辑数据
     *
     * @param  Request  $request
     * @return Response
     */
    public function specificationSave(Request $request)
    {
        $id                     =   $request->json('id');
        $goodsTypeId            =   $request->json('goods_type_id');
        $name                   =   $request->json('name');
        $description            =   $request->json('description');
        $style                  =   $request->json('style');
        $attributeValues        =   array_unique($request->json('attribute_values'));
        $attributeValuesSort    =   $request->json('attribute_values_sort');
        $sort                   =   $request->json('sort');
        $status                 =   $request->json('status');
        
        if (empty($id)) {
            return $this->error('参数错误！');
        }

        if (empty($goodsTypeId)) {
            return $this->error('请选择商品类型！');
        }

        if (empty($name)) {
            return $this->error('属性名称必须填写！');
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 2; //禁用
        }

        $data['goods_type_id']  = $goodsTypeId;
        $data['name']           = $name;
        $data['description']    = $description;
        $data['sort']           = $sort;
        $data['status']         = $status;
        $data['style']          = $style;
        $data['type']           = 2;

        $result = GoodsAttribute::where('id',$id)->update($data);

        $hasAttributeValues = [];
        $hasAttributeIds = [];

        if($result!==false && !empty($attributeValues)) {

            $goodsAttributeValues = GoodsAttributeValue::where('goods_attribute_id',$id)->get();
            
            foreach($goodsAttributeValues as $key => $goodsAttributeValue) {
                foreach($attributeValues as $key1 => $attributeValue) {
                    if($goodsAttributeValue['vname'] == $attributeValue) {

                        // 已存在的属性
                        unset($attributeValues[$key1]);

                        // 已存在的属性id
                        $hasAttributeIds[] = $goodsAttributeValue['id'];

                        $data1['sort'] = $attributeValuesSort[$key1];

                        // 更新数据
                        GoodsAttributeValue::where('id',$goodsAttributeValue['id'])->update($data1);
                    }
                }
            }

            GoodsAttributeValue::whereNotIn('id',$hasAttributeIds)
            ->where('goods_attribute_id',$id)
            ->delete();

            foreach($attributeValues as $key2 => $attributeValue) {
                if($attributeValue) {
                    $data2['goods_attribute_id'] = $id;
                    $data2['vname'] = $attributeValue;
                    $data2['sort'] = $attributeValuesSort[$key2];
                    $result1 = GoodsAttributeValue::create($data2);
                }
            }
        }

        if ($result!==false) {
            return $this->success('操作成功！','specificationIndex');
        } else {
            return $this->error('操作失败！');
        }
    }

    /**
     * 改变数据状态
     *
     * @param  Request  $request
     * @return Response
     */
    public function specificationChangeStatus(Request $request)
    {
        $id = $request->json('id');
        $status = $request->json('status');

        if(empty($id) || empty($status)) {
            return $this->error('参数错误！');
        }

        // 定义对象
        $query = GoodsAttribute::query();

        if(is_array($id)) {
            $query->whereIn('id',$id);
        } else {
            $query->where('id',$id);
        }

        $result = $query->update(['status'=>$status]);

        if ($result) {
            return $this->success('操作成功！');
        } else {
            return $this->error('操作失败！');
        }
    }
}
