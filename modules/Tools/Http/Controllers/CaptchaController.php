<?php

namespace Modules\Tools\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Session;
use Cache;
use Str;

class CaptchaController extends Controller
{
    /**
     * 图形验证码
     * @param  integer
     * @return string
     */
    public function getImage()
    {
        $phrase = new PhraseBuilder;
        // 设置验证码位数
        $code = Str::random(4);
        // 生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        // 设置背景颜色
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(0);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        // 可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        session(['captcha'=>$builder->getPhrase()]);
        return response($builder->output())->header('Content-type','image/jpeg');
    }
}
