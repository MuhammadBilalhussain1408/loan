@extends('website.layouts.master')
@section('title')
    {{$article->title}}
@endsection
@section('meta')
    <meta name="description" content="{{limit_text($article->description)}}">
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{$article->title}}" />
    <meta property="og:description" content="{{limit_text($article->description)}}" />
    <meta property="og:url" content="{{request()->fullUrl()}}" />
    <meta property="og:site_name" content="YoPractice" />
    <meta property="article:modified_time" content="{{$article->created_at}}" />
    <meta property="og:image" content="{{asset('storage/uploads/articles/'.$article->featured_image) }}" />
@endsection
@section('content')
    <!-- ./Page header -->
    <header class="page header text-contrast bg-primary">
        <div class="container">
            <h1 class="bold display-md-4 text-contrast">{{$article->title}}</h1>
        </div>
    </header>
    <div class="position-relative">
        <div class="shape-divider shape-divider-bottom shape-divider-fluid-x text-contrast">
            <svg viewBox="0 0 2880 48" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z"></path>
            </svg>
        </div>
    </div>
    <section class="section blog single">
        <div class="container">
            <div class="row gap-y">
                <div class="col-lg-8 b-r">
                    <div class="blog-post post-content pb-5">
                        @if(!empty($article->featured_image))
                            <figure class="shadow rounded mb-5">
                                <img class="img-responsive rounded"
                                     src="{{asset('storage/uploads/articles/'.$article->featured_image)}}"
                                     alt="">
                            </figure>
                        @endif
                        {!! $article->description !!}

                    </div>
                    <div class="post-author py-5 b-t b-2x">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <h5 class="my-0 bold">{{$article->createdBy->name??''}}</h5>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h4 class="mb-3 bold">Search</h4>
                    <form class="form search-box" action="{{route('website.blog')}}" method="get">
                        <div class="input-group">
                            <input type="text" name="q"
                                   class="form-control rounded-circle-left shadow-none"
                                   placeholder="Search form..." required>
                            <button class="btn rounded-circle-right btn-contrast border-input" type="submit"
                                    data-loading-text="Searching ..."><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    <h4 class="mt-5 mb-3 bold">Latest Posts</h4>
                    <ul class="list-unstyled">
                        @foreach(\App\Models\Article::where('active',1)->orderBy('created_at', 'desc')->limit(5)->get() as $key)
                            <li class="d-flex align-items-center">
                                <a href="{{route('website.blog.details',['article'=>$key->slug])}}"
                                   class="d-flex me-3 py-2">
                                    @if(!empty($key->featured_image))
                                        <img class="rounded-circle icon-xl shadow"
                                             src="{{asset('storage/uploads/articles/'.$key->featured_image)}}"
                                             alt="">
                                    @endif
                                </a>
                                <div class="flex-fill">
                                    <h6 class="semi-bold mt-0 mb-1">
                                        <a href="{{route('website.blog.details',['article'=>$key->slug])}}"
                                           class="text-dark">{{$key->title}}</a>
                                    </h6>
                                    <span class="small text-muted"><i
                                            class="fas fa-calendar"></i> {{$key->created_at->format("M d Y")}}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
