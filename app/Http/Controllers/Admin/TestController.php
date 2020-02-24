<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Helper;
use App\Planet\Form;
use App\Planet\Table;
use App\Models\Post;
use App\Models\Category;
use Quark;

class TestController extends Controller
{
    /**
     * test
     * 
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $form = Quark::form();

        $form->text('username','用户名');
        $form->text('nickname','昵称');
        $form->setAction('api/admin/test/index');

        return $this->success('获取成功！','',$form);
    }
}
