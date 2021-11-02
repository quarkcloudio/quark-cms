<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="{{web_config('WEB_SITE_DESCRIPTION')}} - @yield('description')">
    <meta name="keyword" content="{{web_config('WEB_SITE_KEYWORDS')}} - @yield('keyword')">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="tangtanglove">
    <title>{{web_config('WEB_SITE_NAME')}} - @yield('title')</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair&#43;Display:700,900&amp;display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="{{ asset('css/blog.css') }}?v=1" rel="stylesheet">
  </head>
  <body>
    
<div class="container">
  <header class="blog-header py-3">
    <div class="row flex-nowrap justify-content-between align-items-center">
      <div class="col-4 pt-1">
        <a class="link-secondary" href="#">订阅本站</a>
      </div>
      <div class="col-4 text-center">
        <a class="blog-header-logo text-dark" style="text-decoration:none" href="/">{{web_config('WEB_SITE_NAME')}}</a>
      </div>
      <div class="col-4 d-flex justify-content-end align-items-center">
        <a class="link-secondary" href="#" aria-label="Search">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="mx-3" role="img" viewBox="0 0 24 24"><title>Search</title><circle cx="10.5" cy="10.5" r="7.5"/><path d="M21 21l-5.2-5.2"/></svg>
        </a>
        @if(USERNAME)
        <ul class="navbar-nav">
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
        </ul>
        @else 
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('login') }}">登录</a>
            &nbsp;
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('register') }}">注册</a>
        @endif
      </div>
    </div>
  </header>

  <div class="nav-scroller py-1 mb-2">
    <nav class="nav d-flex justify-content-between">
        @navs($nav, 0)
            @if (isset($nav['_child']))
            <div class="dropdown">
                <a class="p-2 link-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    {!! $nav['title'] !!}
                </a>

                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    @foreach($nav['_child'] as $childKey=>$childValue)
                    <li>
                        <a
                            class="dropdown-item"
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
                    </li>
                    @endforeach
                </ul>
            </div>
            @else
            <a 
              class="p-2 link-secondary {{ get_url_activated($nav['url'],'active') }}"
              @if($nav['url_type'] == 1)
                  href="/article/detail?id={{ $nav['url'] }}"
              @elseif($nav['url_type'] == 2)
                  href="/page/index?id={{ $nav['url'] }}"
              @elseif($nav['url_type'] == 3)
                  href="/article/list?id={{ $nav['url'] }}"
              @elseif($nav['url_type'] == 4)
                  href="{{ $nav['url'] }}"
              @else
                  href="{{ $nav['url'] }}"
              @endif
            >
              {!! $nav['title'] !!}
            </a>
            @endif
        @endnavs
    </nav>
  </div>
</div>

<main class="container">
    @yield('content')
</main>

    <footer class="blog-footer">
    <p>{{web_config('WEB_SITE_COPYRIGHT')}} {!!web_config('WEB_SITE_SCRIPT')!!}</p>
    <p>
        <a href="#">Back to top</a>
    </p>
    </footer>

  </body>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</html>