<!-- 引入模板布局 -->
@extends('layouts.app')
<!-- 定义标题 -->
@section('title', $article->title)
@section('keywords', $article->description)
@section('description', $article->description)

@section('content')
  <div class="mb-4 text-white rounded">
    <div class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            @banners($banner,'IndexBanner')
                <a 
                    @if($banner['url_type'] == 1)
                        href="/article/detail?id={{ $banner['url'] }}"
                    @elseif($banner['url_type'] == 2)
                        href="/page/index?id={{ $banner['url'] }}"
                    @elseif($banner['url_type'] == 3)
                        href="/article/list?id={{ $banner['url'] }}"
                    @elseif($banner['url_type'] == 4)
                        href="{{ $banner['url'] }}"
                    @else
                        href="{{ $banner['url'] }}"
                    @endif
                >
                    <div class="carousel-item active">
                        <img class="d-block w-100 rounded" src="{{ get_picture($banner['cover_id']) }}" alt="First slide">
                    </div>
                </a>
            @endbanners
        </div>
        <a class="carousel-control-prev" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </a>
    </div>
  </div>

  <div class="row g-5">
    <div class="col-md-8">
      <h3 class="pb-4 mb-4 fst-italic border-bottom">
        {{$article->title}}
      </h3>
      
      <article class="blog-post">
        <p>时间：{{date('Y-m-d',strtotime($article->created_at))}} &nbsp;&nbsp;浏览：{{$article->view}}</p>
        {!!$article->content!!}
      </article>
    </div>

    <div class="col-md-4">
      <div class="position-sticky" style="top: 2rem;">
        <div class="p-4 mb-3 bg-light rounded">
          <h4 class="fst-italic">关于我们</h4>
            <p class="mb-0">
                @page($page,'aboutus')
                    {!!strip_tags($page['content'])!!}
                @endpage
            </p>
        </div>

        <div class="p-4">
          <h4 class="fst-italic">文章归档</h4>
          <ol class="list-unstyled mb-0">
            @archives($archive,'posts')
            <li><a href="#">{{$archive['created_date']}}</a></li> 
            @endarchives
          </ol>
        </div>

        <div class="p-4">
          <h4 class="fst-italic">友情链接</h4>
          <ol class="list-unstyled">
            @links($link)
            <li><a href="{{$link['url']}}">{{$link['title']}}</a></li> 
            @endlinks
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- /.container -->
@endsection

<!-- 自定义脚本 -->
@section('script')
<script>
</script>
@endsection