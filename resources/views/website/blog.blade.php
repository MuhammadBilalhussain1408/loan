@extends('website.layouts.master')
@section('title')
    Blog
@endsection
@section('meta')
    <meta name="description" content="Get the latest updates from the best surgery and hospital management software in zimbabwe">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Blog - YoPractice"/>
    <meta property="og:description" content="Get the latest updates from the best surgery and hospital management software in zimbabwe"/>
    <meta property="og:url" content="{{request()->fullUrl()}}"/>
    <meta property="og:site_name" content="YoPractice"/>
    <meta property="article:modified_time" content="{{\Illuminate\Support\Carbon::now()}}"/>
    <meta property="og:image" content="{{asset('website/img/logo.png')}}"/>
@endsection
@section('content')
    <!-- ./Page header -->
    <header class="page header text-contrast bg-primary" style="">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="bold display-md-4 text-contrast mb-4">Blog</h1>
                    <p class="lead text-contrast">Stay tuned with our latest entries</p>
                </div>
            </div>
        </div>
    </header>
    <div class="position-relative">
        <div class="shape-divider shape-divider-bottom shape-divider-fluid-x text-contrast">
            <svg viewBox="0 0 2880 48" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z"></path>
            </svg>
        </div>
    </div>
    <section class="section">
        <div class="container">
            <div class="row gap-y">
                <div class="col-lg-8 b-r">
                    <div class="row gap-y">
                        @foreach($articles as $article)
                            <div class="col-md-6">
                                <div class="card card-blog shadow-box shadow-hover border-0">
                                    <a href="{{route('website.blog.details',['article'=>$article->slug])}}">
                                        @if(!empty($article->featured_image))
                                            <img class="card-img-top img-responsive"
                                                 src="{{asset('storage/uploads/articles/'.$article->featured_image)}}"
                                                 alt="">
                                        @endif
                                    </a>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="author d-flex align-items-center">
                                                <p class="small bold my-0">{{$article->createdBy->name??''}}</p>
                                            </div>
                                            <nav class="nav">
                                                <a href="javascript:;"
                                                   class="d-flex align-items-center text-secondary me-3 blog-action blog-favorite"><i
                                                        class="fas fa-eye me-2"></i> <span
                                                        class="small">{{$article->views}}</span>
                                                </a>
                                            </nav>
                                        </div>
                                        <hr>
                                        <p class="card-title regular">
                                            <a href="{{route('website.blog.details',['article'=>$article->slug])}}">{{$article->title}}</a>
                                        </p>
                                        <p class="card-text text-secondary">
                                            {{$article->excerpt?:limit_text(strip_tags($article->description))}}
                                        </p>
                                        <p class="bold small text-secondary my-0">
                                            <small>{{$article->created_at->format("M d Y")}}</small></p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <nav class="nav justify-content-center mt-5">
                        <a class="btn btn-contrast btn-rounded me-5"
                           href="{{ $articles->previousPageUrl() }}"><i class="fas fa-angle-left"></i> Previous
                        </a>
                        <a class="btn btn-contrast btn-rounded" href="{{ $articles->nextPageUrl() }}">Next <i
                                class="fas fa-angle-right"></i>
                        </a>
                    </nav>
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
