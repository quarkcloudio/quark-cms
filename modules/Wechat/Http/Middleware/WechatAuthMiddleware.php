<?php

namespace Modules\Wechat\Http\Middleware;

use Closure;
use Session;


class WechatAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$guard = null)
    {
        $user = auth()->user();
        
        if(empty($user)) {

            $targetUrl = url('/wechat/login?targetUrl='.urlencode(url()->full()));
            return redirect($targetUrl);
        } else {
            define('WX_UID',$user->id);
            define('WX_USERNAME',$user->name);
            define('WX_NICKNAME',$user->nickname);
            define('WX_PHONE',$user->phone);
            if($user->status != 1) {
                echo "用户被禁用";
                die();
            }
        }

        return $next($request);
    }
}
