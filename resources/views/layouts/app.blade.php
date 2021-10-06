<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="{{web_config('WEB_SITE_DESCRIPTION')}} - @yield('description')">
    <meta name="keyword" content="{{web_config('WEB_SITE_KEYWORDS')}} - @yield('keyword')">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="tangtanglove">
    <title>{{web_config('WEB_SITE_NAME')}} - @yield('title')</title>

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    
    </style>

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

  </head>
  <body>
<div class="container">
    @section('header')
    <header class="blog-header py-3">
        <nav class="navbar navbar-expand-md bg-white border-bottom box-shadow">
            <a class="navbar-brand text-dark" href="#">{{web_config('WEB_SITE_NAME')}}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    @navs($nav,0)
                        @if (isset($nav['_child']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{!! $nav['title'] !!}</a>
                            <div class="dropdown-m
                            enu" aria-labelledby="dropdown01">
                                @foreach($nav['_child'] as $childKey=>$childValue)
                                    <a
                                        class="dropdown-item text-dark"
                                        @if($childValue['url_type'] == 1)
                                            href="/article/detail?id={{ $childValue['url'] }}"
                                        @elseif($childValue['url_type'] == 2)
                                            href="/page/index?id={{ $childValue['url'] }}"
                                        @elseif($childValue['url_type'] == 3)
                                            href="/article/list?id={{ $childValue['url'] }}"
                                        @elseif($childValue['url_type'] == 4)
                                            href="{{ $childValue['url'] }}"
                                        @else
                                            href="{{ $childValue['url'] }}"
                                        @endif
                                    >
                                        {{ $childValue['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                        @else
                        <li class="nav-item {{ get_url_activated($nav['url'],'active') }}">
                            <a class="nav-link text-dark" href="{{ $nav['url'] }}">{!! $nav['title'] !!} <span class="sr-only">{{ get_url_activated($nav['url'],'(current)') }}</span></a>
                        </li>
                        @endif
                    @endnavs
                </ul>
                <form method="get" action="/search/index" class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="text" name="query" placeholder="Search" @if(isset($_GET['query']))value="{{$_GET['query']}}"@endif aria-label="Search">
                    <input class="form-control mr-sm-2" type="hidden" name="module" value="article" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">搜索</button>
                </form>
                <ul class="navbar-nav" style="margin-left:20px;">
                @if(USERNAME)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{USERNAME}}</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item text-dark" href="{{ route('user/index') }}">个人中心</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-dark" href="{{ route('logout') }}">退出</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('order/index') }}">我的订单</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('login') }}">请登录</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('register') }}">免费注册</a>
                    </li>
                @endif
                </ul>
            </div>
        </nav>
    </header>
    @show
    <main role="main">
        @yield('content')
    </main>
    @section('footer')
    <footer class="blog-footer">
        <p>{{web_config('WEB_SITE_COPYRIGHT')}} {!!web_config('WEB_SITE_SCRIPT')!!}</p>
    </footer>
    @show
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}" ></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @section('script')

    @show
  </body>
</html>