<?php

namespace App\Http\Controllers\Admin;

use QuarkCMS\QuarkAdmin\Controllers\QuarkController;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Area;
use App\Models\Merchant;
use App\Models\ShopCategory;
use App\User;
use DB;
use Quark;

class ShopController extends QuarkController
{
    public $title = '商家';

    /**
     * 列表页面
     *
     * @param  Request  $request
     * @return Response
     */
    protected function table()
    {
        $grid = Quark::grid(new Shop)->title($this->title);
        $grid->column('id','ID');
        $grid->column('logo','Logo')->image();
        $grid->column('title','商家名称')->link();
        $grid->column('username','店铺联系人');
        $grid->column('phone','店铺电话');
        $grid->column('created_at','创建时间');
        $grid->column('status','状态')->editable('switch',[
            'on'  => ['value' => 1, 'text' => '正常'],
            'off' => ['value' => 2, 'text' => '禁用']
        ])->width(100);

        $grid->column('actions','操作')->width(100)->rowActions(function($rowAction) {
            $rowAction->menu('edit', '编辑');
            $rowAction->menu('show', '显示');
            $rowAction->menu('delete', '删除')->model(function($model) {
                $model->delete();
            })->withConfirm('确认要删除吗？','删除后数据将无法恢复，请谨慎操作！');
        });

        // 头部操作
        $grid->actions(function($action) {
            $action->button('create', '新增');
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

            $search->equal('status', '所选状态')
            ->select([''=>'全部',1=>'正常',2=>'已禁用'])
            ->placeholder('选择状态')
            ->width(110);

            $search->where('title', '搜索内容',function ($query) {
                $query->where('title', 'like', "%{input}%");
            })->placeholder('搜索内容');

            $search->between('created_at', '创建时间')->datetime()->advanced();
        })->expand(false);

        $grid->model()->paginate(10);

        return $grid;
    }

    /**
     * 表单页面
     * 
     * @param  Request  $request
     * @return Response
     */
    protected function form()
    {
        $form = Quark::form(new Shop);

        $title = $form->isCreating() ? '创建'.$this->title : '编辑'.$this->title;
        $form->title($title);

        if($form->isEditing()) {

            // 编辑商铺
            $id = request('id');

            // 读取商铺信息
            $shopInfo = Shop::where('id',$id)->first();
        }

        $form->tab('基本信息', function ($form) {
            $form->id('id','ID');

            $form->text('title','商家名称')
            ->rules(['required','max:190'],['required'=>'标题必须填写','max'=>'名称不能超过190个字符']);

            $form->image('logo','Logo')->mode('single');

            $categorys = [];
            $getCategorys = ShopCategory::where('status',1)->get()->toArray();
            $categoryTrees = list_to_tree($getCategorys,'id','pid','children',0);
            $categoryTreeLists = tree_to_ordered_list($categoryTrees,0,'name','children');

            foreach ($categoryTreeLists as $key => $value) {
                $categorys[$value['id']] = $value['title'];
            }

            $form->select('category_id','分类')
            ->options($categorys)
            ->rules(['required'],['required'=>'请选择分类'])
            ->width(200);

            if(isset($shopInfo)) {

                $merchantInfo = Merchant::where('id',$shopInfo['mch_id'])->first();

                $bindUser = User::where('id',$merchantInfo['uid'])->first();

                $form->search('uid','绑定用户')
                ->rules(['required'],['required'=>'请选择用户'])
                ->options([
                    $bindUser['id'] => $bindUser['username']
                ])
                ->ajax('admin/user/suggest')
                ->value($bindUser['id'])
                ->width(200);
            } else {
                $form->search('uid','绑定用户')
                ->rules(['required'],['required'=>'请选择用户'])
                ->ajax('admin/user/suggest')
                ->width(200);
            }

            $form->text('tags','标签');

            $form->textArea('description','描述')
            ->rules(['max:190'],['max'=>'名称不能超过190个字符']);

            $form->image('cover_ids','封面图')->mode('multiple');

            $form->editor('content','内容');

            $form->text('username','联系人');
            $form->text('phone','商家电话');

            $form->checkbox('open_days','营业日期')->options([
                1 => '周一',
                2 => '周二',
                3 => '周三',
                4 => '周四',
                5 => '周五',
                6 => '周六',
                7 => '周日'
            ]);

            $form->timeRange('open_times','营业时间')->format('H:mm')->value(['00:00','23:59']);

            $form->switch('open_status','营业状态')->options([
                'on'  => '营业',
                'off' => '打烊'
            ])->default(true);

            $form->switch('status','状态')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(true);

        })->tab('扩展信息', function ($form) {

            $form->number('level','排序')->extra('越大越靠前')->value(0);

            $form->checkbox('position','推荐位')->options([
                1 => '首页推荐',
                2 => '频道推荐',
                3 => '列表推荐',
                4 => '详情推荐'
            ]);

            $form->switch('is_self','自营')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(false);

            $form->switch('comment_status','允许评论')->options([
                'on'  => '是',
                'off' => '否'
            ])->default(true);

        })->tab('商铺位置', function ($form) {

            $areas = Area::where('pid','<>',0)
            ->select('area_name as value','area_name as label','id','pid')
            ->get()
            ->toArray();

            $options = list_to_tree($areas,'id','pid','children',1);

            $form->cascader('area','商家地域')->options($options)->width(400);

            $form->text('address','详细地址')->width(400);

            if(isset($shopInfo)) {
                // 地图坐标
                $form->map('map','商家坐标')
                ->style(['width'=>'100%','height'=>400])
                ->position($shopInfo['longitude'],$shopInfo['latitude']);
            } else {
                $form->map('map','商家坐标')
                ->style(['width'=>'100%','height'=>400]);
            }

        })->tab('资质证件', function ($form) {
            $form->text('corporate_name','法人姓名');
            $form->text('corporate_idcard','身份证号');
            $form->image('corporate_idcard_cover_id','身份证照片')->mode('single');
            $form->image('business_license_cover_id','营业执照')->mode('single');
        })->tab('打款信息', function ($form) {
            $form->text('bank_name','开户行');
            $form->text('bank_payee','收款人');
            $form->text('bank_number','银行账号');
        });

        return $form;
    }

    /**
     * 保存方法
     * 
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $title                      =   $request->json('title','');
        $logo                       =   $request->json('logo','');
        $uid                        =   $request->json('uid','');
        $categoryId                 =   $request->json('category_id',0);
        $tags                       =   $request->json('tags','');
        $description                =   $request->json('description');
        $content                    =   $request->json('content','');
        $coverIds                   =   $request->json('cover_ids');
        $level                      =   $request->json('level','');
        $position                   =   $request->json('position','');
        $username                   =   $request->json('username','');
        $phone                      =   $request->json('phone',0);
        $area                       =   $request->json('area');
        $address                    =   $request->json('address');
        $map                        =   $request->json('map');
        $businessLicenseCoverId     =   $request->json('business_license_cover_id');
        $corporateName              =   $request->json('corporate_name');
        $corporateIdcard            =   $request->json('corporate_idcard');
        $corporateIdcardCoverId     =   $request->json('corporate_idcard_cover_id');
        $comment                    =   $request->json('comment');
        $view                       =   $request->json('view');
        $commentStatus              =   $request->json('comment_status');
        $rate                       =   $request->json('rate');
        $openDays                   =   $request->json('open_days');
        $openTimes                  =   $request->json('open_times');
        $openStatus                 =   $request->json('open_status');
        $isSelf                     =   $request->json('is_self');
        $status                     =   $request->json('status');

        $bankName                  =   $request->json('bank_name');
        $bankPayee                 =   $request->json('bank_payee');
        $bankNumber                =   $request->json('bank_number');

        if(empty($title)) {
            return error('商家名称不能为空！');
        }

        if(empty($logo)) {
            return error('请上传logo！');
        }

        if(empty($uid)) {
            return error('请选择绑定用户！');
        }

        if(empty($categoryId)) {
            return error('请选择分类！');
        }

        if(empty($username)) {
            return error('商家联系人不能为空！');
        }

        if(empty($phone)) {
            return error('商家电话不能为空！');
        }

        $hasMerchant = Merchant::where('uid',$uid)->first();

        if(!empty($hasMerchant)) {
            $data['mch_id'] = $hasMerchant['id'];
        } else {
            $merchantData['uid'] = $uid;
            $merchantData['bank_name'] = $bankName;
            $merchantData['bank_payee'] = $bankPayee;
            $merchantData['bank_number'] = $bankNumber;
            $merchantInfo = Merchant::create($merchantData);
            $data['mch_id'] = $merchantInfo['id'];
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 0;
        }

        if ($openStatus == true) {
            $openStatus = 1;
        } else {
            $openStatus = 0;
        }

        if ($commentStatus == true) {
            $commentStatus = 1;
        } else {
            $commentStatus = 0;
        }

        $data['title'] = $title;
        $data['logo'] = $logo;
        $data['category_id'] = $categoryId;
        $data['tags'] = $tags;
        $data['description'] = $description;
        $data['content'] = $content;
        $data['cover_ids'] = json_encode($coverIds);
        $data['level'] = $level;
        if($position) {
            $data['position'] = collect($position)->sum();
        }
        $data['username'] = $username;
        $data['phone'] = $phone;
        $data['province'] = $area[0];
        $data['city'] = $area[1];
        $data['county'] = $area[2];
        $data['address'] = $address;

        if($map) {
            $data['longitude'] = $map['longitude'];
            $data['latitude'] = $map['latitude'];
        }

        if($businessLicenseCoverId) {
            $data['business_license_cover_id'] = $businessLicenseCoverId[0]['id'];
        }

        $data['corporate_name'] = $corporateName;
        $data['corporate_idcard'] = $corporateIdcard;

        if($corporateIdcardCoverId) {
            $data['corporate_idcard_cover_id'] = $corporateIdcardCoverId[0]['id'];
        }

        $data['comment_status'] = $commentStatus;
        $data['open_days'] = json_encode($openDays);

        $getOpenTimes = [date("H:i", strtotime($openTimes[0])),date("H:i", strtotime($openTimes[1]))];

        $data['open_times'] = json_encode($getOpenTimes);
        $data['open_status'] = $openStatus;
        $data['is_self'] = $isSelf;
        $data['status'] = $status;

        $result = Shop::create($data);

        if ($result) {
            return success('操作成功！','/quark/engine?api=admin/shop/index&component=table');
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
    public function update(Request $request)
    {
        $id                         =   $request->json('id');
        $title                      =   $request->json('title','');
        $logo                       =   $request->json('logo','');
        $uid                        =   $request->json('uid','');
        $categoryId                 =   $request->json('category_id',0);
        $tags                       =   $request->json('tags','');
        $description                =   $request->json('description');
        $content                    =   $request->json('content','');
        $coverIds                   =   $request->json('cover_ids');
        $level                      =   $request->json('level','');
        $position                   =   $request->json('position','');
        $username                   =   $request->json('username','');
        $phone                      =   $request->json('phone',0);
        $area                       =   $request->json('area');
        $address                    =   $request->json('address');
        $map                        =   $request->json('map');
        $businessLicenseCoverId     =   $request->json('business_license_cover_id');
        $corporateName              =   $request->json('corporate_name');
        $corporateIdcard            =   $request->json('corporate_idcard');
        $corporateIdcardCoverId     =   $request->json('corporate_idcard_cover_id');
        $comment                    =   $request->json('comment');
        $view                       =   $request->json('view');
        $commentStatus              =   $request->json('comment_status');
        $rate                       =   $request->json('rate');
        $openDays                   =   $request->json('open_days');
        $openTimes                  =   $request->json('open_times');
        $openStatus                 =   $request->json('open_status');
        $isSelf                     =   $request->json('is_self');
        $status                     =   $request->json('status');

        $bankName                  =   $request->json('bank_name');
        $bankPayee                 =   $request->json('bank_payee');
        $bankNumber                =   $request->json('bank_number');

        if(empty($title)) {
            return error('商家名称不能为空！');
        }

        if(empty($logo)) {
            return error('请上传logo！');
        }

        if(empty($uid)) {
            return error('请选择绑定用户！');
        }

        if(empty($categoryId)) {
            return error('请选择分类！');
        }

        if(empty($username)) {
            return error('商家联系人不能为空！');
        }

        if(empty($phone)) {
            return error('商家电话不能为空！');
        }

        $hasMerchant = Merchant::where('uid',$uid)->first();

        if(!empty($hasMerchant)) {
            $data['mch_id'] = $hasMerchant['id'];
            $merchantData['uid'] = $uid;
            $merchantData['bank_name'] = $bankName;
            $merchantData['bank_payee'] = $bankPayee;
            $merchantData['bank_number'] = $bankNumber;
            $merchantInfo = Merchant::where('id',$hasMerchant['id'])->update($merchantData);
        } else {
            $merchantData['uid'] = $uid;
            $merchantData['bank_name'] = $bankName;
            $merchantData['bank_payee'] = $bankPayee;
            $merchantData['bank_number'] = $bankNumber;
            $merchantInfo = Merchant::create($merchantData);
            $data['mch_id'] = $merchantInfo['id'];
        }

        if ($status == true) {
            $status = 1;
        } else {
            $status = 2;
        }

        if ($openStatus == true) {
            $openStatus = 1;
        } else {
            $openStatus = 2;
        }

        if ($commentStatus == true) {
            $commentStatus = 1;
        } else {
            $commentStatus = 2;
        }

        $data['title'] = $title;
        $data['logo'] = $logo;
        $data['category_id'] = $categoryId;
        $data['tags'] = $tags;
        $data['description'] = $description;
        $data['content'] = $content;
        $data['cover_ids'] = json_encode($coverIds);
        $data['level'] = $level;
        if($position) {
            $data['position'] = collect($position)->sum();
        }
        $data['username'] = $username;
        $data['phone'] = $phone;
        $data['province'] = $area[0];
        $data['city'] = $area[1];
        $data['county'] = $area[2];
        $data['address'] = $address;

        if($map) {
            $data['longitude'] = $map['longitude'];
            $data['latitude'] = $map['latitude'];
        }

        if($businessLicenseCoverId) {
            $data['business_license_cover_id'] = $businessLicenseCoverId[0]['id'];
        }

        $data['corporate_name'] = $corporateName;
        $data['corporate_idcard'] = $corporateIdcard;

        if($corporateIdcardCoverId) {
            $data['corporate_idcard_cover_id'] = $corporateIdcardCoverId[0]['id'];
        }

        $data['comment_status'] = $commentStatus;
        $data['open_days'] = json_encode($openDays);

        $getOpenTimes = [date("H:i", strtotime($openTimes[0])),date("H:i", strtotime($openTimes[1]))];

        $data['open_times'] = json_encode($getOpenTimes);
        $data['open_status'] = $openStatus;
        $data['is_self'] = $isSelf;
        $data['status'] = $status;

        $result = Shop::where('id',$id)->update($data);

        if ($result) {
            return success('操作成功！','/quark/engine?api=admin/shop/index&component=table');
        } else {
            return error('操作失败！');
        }
    }
}
