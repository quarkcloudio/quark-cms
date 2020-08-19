<?php

namespace Modules\Mall\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use Modules\Mall\Models\GoodsType;
use Modules\Mall\Models\GoodsAttribute;
use Modules\Mall\Models\GoodsAttributeValue;
use Modules\Mall\Models\GoodsCategory;
use Modules\Mall\Models\GoodsCategoryRelationship;
use Modules\Mall\Models\GoodsBrand;
use Modules\Mall\Models\GoodsCategoryAttribute;
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

        foreach ($categoryTreeLists as $key => $categoryTreeList) {
            $getCategorys[$key]['name'] = $categoryTreeList['title'];
            $getCategorys[$key]['value'] = $categoryTreeList['id'];
        }

        $goodsBrands = GoodsBrand::where('status',1)->select('id as key','name as title')->get();

        $data['categorys'] = $getCategorys;
        $data['goodsBrands'] = $goodsBrands;
        $data['goodsBrandSelectedKeys'] = [];

        $getGoodsTypes = GoodsType::where('status',1)->select('name','id as value')->get();

        foreach ($getGoodsTypes as $key => $getGoodsType) {
            $goodsTypes[$key]['name'] = $getGoodsType['name'];
            $goodsTypes[$key]['value'] = $getGoodsType['value'];
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
        $title          =   $request->json('title','');
        $name           =   $request->json('name');
        $description    =   $request->json('description');
        $sort           =   $request->json('sort');
        $pid            =   $request->json('pid');
        $coverId        =   $request->json('cover_id');
        $indexTpl       =   $request->json('index_tpl');
        $listsTpl       =   $request->json('lists_tpl');
        $detailTpl      =   $request->json('detail_tpl');
        $pageNum        =   $request->json('page_num');
        $status         =   $request->json('status');
        $brandIds       =   $request->json('brand_ids');

        $attributes          =   $request->json('attributes');
        $specifications      =   $request->json('specifications');
        
        if (empty($title)) {
            return error('标题必须填写！');
        }

        if (empty($name)) {
            return error('名称必须填写！');
        }

        if (empty($pageNum)) {
            return error('分页数量必须填写！');
        }

        $hasTitle = GoodsCategory::where('title',$title)->where('status',1)->first();

        if($hasTitle) {
            return error('此分类标题已存在！');
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 2; //禁用
        }
        
        if($coverId) {
            $coverId = $coverId[0]['id'];
        } else {
            $coverId = 0;
        }

        $data['title']          = $title;
        $data['name']           = $name;
        $data['description']    = $description;
        $data['sort']           = $sort;
        $data['pid']            = $pid;
        $data['cover_id']       = $coverId;
        $data['index_tpl']      = $indexTpl;
        $data['lists_tpl']      = $listsTpl;
        $data['detail_tpl']     = $detailTpl;
        $data['page_num']       = $pageNum;
        $data['status']         = $status;
        $data['goods_brand_ids'] = json_encode($brandIds);

        $result = GoodsCategory::create($data);

        if($result) {
            if($attributes) {
                foreach ($attributes as $key => $attribute) {
                    $data1['goods_category_id'] = $result->id;
                    $data1['goods_attribute_id'] = $attribute['id'];

                    if(isset($attribute['group'])) {
                        $data1['gorup_name'] = $attribute['group'];
                    }

                    if(isset($attribute['sort'])) {
                        $data1['sort'] = $attribute['sort'];
                    }

                    $data1['type'] = 1;
                    GoodsCategoryAttribute::create($data1);
                }
            }

            if($specifications) {
                foreach ($specifications as $key => $specification) {
                    $data2['goods_category_id'] = $result->id;
                    $data2['goods_attribute_id'] = $specification['id'];

                    if(isset($specification['group'])) {
                        $data2['gorup_name'] = $specification['group'];
                    }

                    if(isset($specification['sort'])) {
                        $data2['sort'] = $specification['sort'];
                    }

                    $data2['type'] = 2;
                    GoodsCategoryAttribute::create($data2);
                }
            }
        }

        if($result) {
            return success('操作成功！','/quark/engine?api=admin/goodsCategory/index&component=table');
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

        $categorys         = GoodsCategory::where('status',1)->get()->toArray();
        $categoryTrees     = list_to_tree($categorys);
        $categoryTreeLists = tree_to_ordered_list($categoryTrees,0,'title');

        // 模板数据
        $getCategorys = [];

        foreach ($categoryTreeLists as $key => $categoryTreeList) {
            $getCategorys[$key]['name'] = $categoryTreeList['title'];
            $getCategorys[$key]['value'] = $categoryTreeList['id'];
        }

        $data = GoodsCategory::find($id)->toArray();

        $cover_id = $data['cover_id'];

        if($cover_id) {
            unset($data['cover_id']);
            $data['cover_id'][0]['id'] =$cover_id;
            $data['cover_id'][0]['uid'] =$cover_id;
            $data['cover_id'][0]['name'] = get_picture($cover_id,'name');
            $data['cover_id'][0]['url'] = get_picture($cover_id);
        }

        $goodsBrands = GoodsBrand::where('status',1)->select('id as key','name as title')->get();

        $data['categorys'] = $getCategorys;
        $data['goodsBrands'] = $goodsBrands;
        $data['goodsBrandSelectedKeys'] = json_decode($data['goods_brand_ids']);

        $getGoodsTypes = GoodsType::where('status',1)->select('name','id as value')->get();

        foreach ($getGoodsTypes as $key => $getGoodsType) {
            $goodsTypes[$key]['name'] = $getGoodsType['name'];
            $goodsTypes[$key]['value'] = $getGoodsType['value'];
        }

        $data['goodsTypes'] = $goodsTypes;

        $attributeSelectedIds = GoodsCategoryAttribute::where('goods_category_id',$id)
        ->where('type',1)
        ->pluck('goods_attribute_id');
        $data['attributeSelectedIds'] = $attributeSelectedIds;

        $specificationSelectedIds = GoodsCategoryAttribute::where('goods_category_id',$id)
        ->where('type',2)
        ->pluck('goods_attribute_id');
        $data['specificationSelectedIds'] = $specificationSelectedIds;

        // 定义对象
        $attributeSelectedDatas = GoodsAttribute::whereIn('id', $attributeSelectedIds)
        ->where('status', '>', 0)
        ->where('type', 1)
        ->orderBy('id', 'desc')
        ->get()
        ->toArray();

        $data['attributeSelectedKeys'] = [];

        foreach ($attributeSelectedDatas as $key => $attributeSelectedData) {
            $goodsAttributeValues = GoodsAttributeValue::where('goods_attribute_id',$attributeSelectedData['id'])->pluck('vname')->toArray();
            $attributeSelectedDatas[$key]['goods_attribute_values'] = implode(',',$goodsAttributeValues);
            $data['attributeSelectedKeys'][] = $key;
        }

        $data['attributeSelectedData'] = $attributeSelectedDatas;

        // 定义对象
        $specificationSelectedDatas = GoodsAttribute::whereIn('id', $specificationSelectedIds)
        ->where('status', '>', 0)
        ->where('type', 2)
        ->orderBy('id', 'desc')
        ->get()
        ->toArray();

        $data['specificationSelectedKeys'] = [];

        foreach ($specificationSelectedDatas as $key => $specificationSelectedData) {
            $goodsAttributeValues = GoodsAttributeValue::where('goods_attribute_id',$specificationSelectedData['id'])->pluck('vname')->toArray();
            $specificationSelectedDatas[$key]['goods_attribute_values'] = implode(',',$goodsAttributeValues);
            $data['specificationSelectedKeys'][] = $key;
        }

        $data['specificationSelectedData'] = $specificationSelectedDatas;

        if ($data['status'] == 1) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        
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
        $id             =   $request->json('id');
        $title          =   $request->json('title','');
        $name           =   $request->json('name');
        $description    =   $request->json('description');
        $sort           =   $request->json('sort');
        $pid            =   $request->json('pid');
        $coverId        =   $request->json('cover_id');
        $indexTpl       =   $request->json('index_tpl');
        $listsTpl       =   $request->json('lists_tpl');
        $detailTpl      =   $request->json('detail_tpl');
        $pageNum        =   $request->json('page_num');
        $status         =   $request->json('status');
        $brandIds       =   $request->json('brand_ids');

        $attributes          =   $request->json('attributes');
        $specifications      =   $request->json('specifications');
        
        if (empty($title)) {
            return $this->error('标题必须填写！');
        }

        if (empty($name)) {
            return $this->error('名称必须填写！');
        }

        if (empty($pageNum)) {
            return $this->error('分页数量必须填写！');
        }

        $hasTitle = GoodsCategory::where('id','<>',$id)->where('title',$title)->where('status',1)->first();

        if($hasTitle) {
            return $this->error('此分类标题已存在！');
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 0; //禁用
        }
        
        if($coverId) {
            $coverId = $coverId[0]['id'];
        } else {
            $coverId = 0;
        }

        $data['title']          = $title;
        $data['name']           = $name;
        $data['description']    = $description;
        $data['sort']           = $sort;
        $data['pid']            = $pid;
        $data['cover_id']       = $coverId;
        $data['index_tpl']      = $indexTpl;
        $data['lists_tpl']      = $listsTpl;
        $data['detail_tpl']     = $detailTpl;
        $data['page_num']       = $pageNum;
        $data['status']         = $status;
        $data['goods_brand_ids'] = json_encode($brandIds);

        $result = GoodsCategory::where('id',$id)->update($data);

        if($result !== false) {
            GoodsCategoryAttribute::where('goods_category_id',$id)->delete();

            if($attributes) {
                foreach ($attributes as $key => $attribute) {
                    $data1['goods_category_id'] = $id;
                    $data1['goods_attribute_id'] = $attribute['id'];

                    if(isset($attribute['group'])) {
                        $data1['gorup_name'] = $attribute['group'];
                    }

                    if(isset($attribute['sort'])) {
                        $data1['sort'] = $attribute['sort'];
                    }

                    $data1['type'] = 1;
                    GoodsCategoryAttribute::create($data1);
                }
            }
            
            if($specifications) {
                foreach ($specifications as $key => $specification) {
                    $data2['goods_category_id'] = $id;
                    $data2['goods_attribute_id'] = $specification['id'];

                    if(isset($specification['group'])) {
                        $data2['gorup_name'] = $specification['group'];
                    }

                    if(isset($specification['sort'])) {
                        $data2['sort'] = $specification['sort'];
                    }

                    $data2['type'] = 2;
                    GoodsCategoryAttribute::create($data2);
                }
            }
        }

        if ($result !== false) {
            return success('操作成功！','/quark/engine?api=admin/goodsCategory/index&component=table');
        } else {
            return error('操作失败！');
        }
    }
}
