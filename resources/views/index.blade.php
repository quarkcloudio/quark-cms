<!-- 引入模板布局 -->
@extends('layouts.app')
<!-- 定义标题 -->
@section('title', '官网')
@section('keywords', '官网')
@section('description', '官网')

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

  <div class="row mb-2">
    @articles($article,'default',2,0,1)
    <div class="col-md-6">
      <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <strong class="d-inline-block mb-2 text-primary">{{ get_category($article['category_id']) }}</strong>
          <h3 class="mb-0">{{ msubstr($article['title'],0,30) }}</h3>
          <div class="mb-1 text-muted">{{date('Y-m-d',strtotime($article['created_at']))}}</div>
          <p class="card-text mb-auto">{{ msubstr($article['description'],0,50) }}</p>
          <a href="/article/detail?id={{$article['id']}}" class="stretched-link">查看更多</a>
        </div>
        <div class="col-auto d-none d-lg-block">
            <img class="bd-placeholder-img" width="200" height="250" src="{{ get_picture($article['cover_id']) }}" alt="{{ msubstr($article['title'],0,30) }}">
        </div>
      </div>
    </div>
    @endarticles
  </div>


  <div class="row g-5">
    <div class="col-md-8">
      <h3 class="pb-4 mb-4 fst-italic border-bottom">
        所有文章
      </h3>
      
    @foreach($articles as $key => $article)
      <article class="blog-post">
        <h2 class="blog-post-title"><a style="color:#212529;text-decoration:none" href="/article/detail?id={{$article['id']}}">{{ msubstr($article['title'],0,30) }}</a></h2>
        <p class="blog-post-meta">{{date('Y-m-d',strtotime($article['created_at']))}}</p>

        <p>{!!strip_tags($article['description'])!!}</p>
      </article>
     @endforeach

      <nav class="blog-pagination" aria-label="Pagination">
        @if($category)
            {{ $articles->appends(['name'=>$category->name])->links() }}
        @else
            {{ $articles->links() }}
        @endif
      </nav>

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