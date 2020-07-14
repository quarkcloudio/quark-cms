<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use EasyWeChat\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // layout布局使用基于闭包的composers
        view()->composer('*', function ($view) {
            $user = Auth::user();

            $uid = '';
            $username = '';
            $nickname = '';

            if(!empty($user)) {
                $uid = $user->id;
                $username = $user->username;
                $nickname = $user->nickname;
            }

            $jsApi = '';

            if(is_wechat()) {
                if(wechat_config() != false) {
                    $app = Factory::officialAccount(wechat_config());
                    $jsApi = $app->jssdk->buildConfig(array('onMenuShareTimeline','onMenuShareAppMessage'), $debug = false, $beta = false, $json = true);
                } 
            }

            $view->with('uid', $uid)
            ->with('username', $username)
            ->with('nickname', $nickname)
            ->with('jsApi', $jsApi);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
