<?php

namespace App\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use App\Models\GoodsType;
use App\Models\GoodsAttribute;
use App\Models\GoodsAttributeValue;
use App\Models\GoodsCategory;
use App\Models\GoodsCategoryRelationship;
use App\Models\GoodsBrand;
use DB;
use Quark;

class GoodsCategoryController extends QuarkController
{
    public $title = '商品分类';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new GoodsCategory)->title($this->title)->tree();
        $grid->column('id','ID');
        $grid->column('title','标题')->link('#/admin/goodsCategory/edit');
        $grid->column('sort','排序')->editable()->sorter()->width(100);
        $grid->column('name','缩略名');
        $grid->column('page_num','分页数量');
        $grid->column('created_at','创建时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 0, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('myEdit', '编辑')->link('#/admin/goodsCategory/edit');
            $rowAction->menu('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('myCreate', '新增')->type('primary')->link('#/admin/goodsCategory/create');
            $action->button('refresh', '刷新');
        });

        // select样式的批量操作
        $grid->batchActions(function($batch) {
            $batch->option('', '批量操作');
            $batch->option('resume', '启用')->model(function($model) {
                $model->update(['status'=>1]);
            });
            $batch->option('forbid', '禁用')->model(function($model) {
                $model->update(['status'=>0]);
            });
            $batch->option('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        })->style('select',['width'=>120]);

        $grid->search(function($search) {

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',0=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110);

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

        })->expand(false);

        $grid->model()->paginate(1000);

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
        $categorys         = GoodsCategory::where('status',1)->get()->toArray();
        $categoryTrees     = list_to_tree($categorys);
        $categoryTreeLists = tree_to_ordered_list($categoryTrees,0,'title');

        // 模板数据
        $getCategorys = [];

        $getCategorys[0]['name'] = '请选择分类';
        $getCategorys[0]['value'] = '0';

        foreach ($categoryTreeLists as $key => $categoryTreeList) {
            $getCategorys[$key+1]['name'] = $categoryTreeList['title'];
            $getCategorys[$key+1]['value'] = $categoryTreeList['id'];
        }

        $goodsBrands = GoodsBrand::where('status',1)->select('id as key','name as title')->get();

        $data['categorys'] = $getCategorys;
        $data['goodsBrands'] = $goodsBrands;
        $data['goodsBrandSelectedKeys'] = [];

        $goodsTypes[0]['name'] = '请选择商品类型';
        $goodsTypes[0]['value'] = '0';

        $getGoodsTypes = GoodsType::where('status',1)->select('name','id as value')->get();

        foreach ($getGoodsTypes as $key => $getGoodsType) {
            $goodsTypes[$key+1]['name'] = $getGoodsType['name'];
            $goodsTypes[$key+1]['value'] = $getGoodsType['value'];
        }

        $data['goodsTypes'] = $goodsTypes;

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
            $status = 0; //禁用
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
                $data1['vname'] = $attributeValue['vname'];
                if(isset($attributeValue['sort'])) {
                    $data1['sort'] = $attributeValue['sort'];
                }

                $result1 = GoodsAttributeValue::create($data1);
            }
        }

        if($result) {
            return success('操作成功！','/quark/engine?api=admin/goodsAttribute/index&component=table');
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

        $goodsAttribute = GoodsAttribute::find($id)->toArray();

        $goodsAttributeValues = GoodsAttributeValue::where('goods_attribute_id',$goodsAttribute['id'])->get();

        $goodsAttribute['attribute_values'] = $goodsAttributeValues;

        $data['goods_attribute'] = $goodsAttribute;
        $data['goods_types'] = GoodsType::where('status',1)->get();
        
        if(!empty($data)) {
            return success('操作成功！','',$data);
        } else {
            return error('操作失败！');
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
        $attributeValues        =   $request->json('attribute_values');
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
            $status = 0; //禁用
        }

        $data['goods_type_id']  = $goodsTypeId;
        $data['name']           = $name;
        $data['description']    = $description;
        $data['sort']           = $sort;
        $data['status']         = $status;
        $data['style']          = $style;
        $data['type']           = 1;

        $result = GoodsAttribute::where('id',$id)->update($data);

        $hasAttributeIds = [];

        if($result!==false && !empty($attributeValues)) {

            foreach($attributeValues as $key1 => $attributeValue) {
                if(isset($attributeValue['id'])) {
                    $hasAttributeIds[] = $attributeValue['id'];
                }
            }

            // 删除去掉的属性
            GoodsAttributeValue::whereNotIn('id',$hasAttributeIds)
            ->where('goods_attribute_id',$id)
            ->delete();

            foreach($attributeValues as $key1 => $attributeValue) {
                if(isset($attributeValue['id'])) {

                    // 已存在的属性
                    $data1['sort'] = $attributeValue['sort'];
                    $data1['vname'] = $attributeValue['vname'];

                    // 更新数据
                    GoodsAttributeValue::where('id',$attributeValue['id'])->update($data1);
                } else {
                    // 不存在的属性id
                    $data1['goods_attribute_id'] = $id;
                    $data1['sort'] = $attributeValue['sort'];
                    $data1['vname'] = $attributeValue['vname'];

                    // 创建数据
                    GoodsAttributeValue::create($data1);
                }
            }
        }

        if ($result !== false) {
            return success('操作成功！','/quark/engine?api=admin/goodsAttribute/index&component=table');
        } else {
            return error('操作失败！');
        }
    }
}
