<?php

namespace App\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use App\Models\GoodsType;
use App\Models\GoodsAttribute;
use App\Models\GoodsAttributeValue;
use DB;
use Quark;

class GoodsAttributeController extends QuarkController
{
    public $title = '商品属性';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new GoodsAttribute)->title($this->title);
        $grid->column('id','ID');
        $grid->column('name','属性名称')->link();
        $grid->column('goodsType.name','属性类型');
        $grid->column('description','属性描述');
        $grid->column('style','显示样式')->using([1 => '多选', 2 => '单选', 3 => '文本']);
        $grid->column('sort','排序')->editable()->sorter()->width(100);
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 2, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('edit', '编辑');
            $rowAction->menu('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('myCreate', '新增')->link('#/goodsAttribute/create');
            $action->button('refresh', '刷新');
        });

        // select样式的批量操作
        $grid->batchActions(function($batch) {
            $batch->option('', '批量操作');
            $batch->option('resume', '启用')->model(function($model) {
                $model->update(['status'=>1]);
            });
            $batch->option('forbid', '禁用')->model(function($model) {
                $model->update(['status'=>2]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {

            $goodsTypes = GoodsType::where('status',1)->get();

            $options[''] = '全部';
            foreach ($goodsTypes as $key => $value) {
                $options[$value['id']] = $value['name'];
            }

            $search->equal('goods_type_id', '商品类型')->select($options)->placeholder('请选择商品类型')->width(110);

            $search->where('name', '搜索内容',function ($query) {
                $query->where('name', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',2=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110)
            ->advanced();

        })->expand(false);

        $grid->model()->paginate(10);

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
        $id   =   $request->json('id');

        $data['goods_types'] = GoodsType::where('status',1)->get();

        if(!empty($data)) {
            return success('获取成功！','',$data);
        } else {
            return error('获取失败！');
        }
    }

    /**
     * 保存方法
     * 
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
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
        $data['type']           = 1;

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
            return $this->success('操作成功！','attributeIndex');
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
    public function edit(Request $request)
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
    public function save(Request $request)
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
        $data['type']           = 1;

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
            return $this->success('操作成功！','attributeIndex');
        } else {
            return $this->error('操作失败！');
        }
    }
}
