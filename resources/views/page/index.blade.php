<!-- 引入模板布局 -->
@extends('layouts.app')
<!-- 定义标题 -->
@section('title', $page->title)
@section('keywords', $page->description)
@section('description', $page->description)

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
    <div class="col-md-12">
      <h3 class="pb-4 mb-4 fst-italic border-bottom">
        {{$page->title}}
      </h3>
      <article class="blog-post">
        {!!$page->content!!}
      </article>
    </div> 
  </div>
  <!-- /.container -->
@endsection

<!-- 自定义脚本 -->
@section('script')
<script>
</script>
@endsection