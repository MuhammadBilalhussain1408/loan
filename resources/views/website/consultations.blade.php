@extends('website.layouts.master')
@section('title')
    Consultations - Medical Software for your Independent Practice
@endsection
@section('meta')
    <meta name="description" content="Consultations">
    <meta name="keywords" content="car repair zimbabwe,car repair harare, best car repair harare, auto harare, auto zimbabwe, auto repair,best auto repair harare">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Ignition"/>
    <meta property="og:description" content="We are your one stop shop for all your motor vehicle servicing and repair solutions in Zimbabwe. All our mechanics are certified car repair specialists."/>
    <meta property="og:url" content="{{request()->fullUrl()}}"/>
    <meta property="og:site_name" content="Ignition"/>
    <meta property="og:image" content="{{asset('website/img/logo.png')}}"/>
@endsection
@section('content')
    <!-- ./Page header -->
    <header
        class="header alter2-header section parallax image-background center-bottom cover overlay overlay-primary alpha-8 text-contrast"
        style="background-image: url('{{asset('website/img/bg/grid.jpg')}}')">
        <div class="divider-shape">
            <!-- waves divider -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"
                 class="shape-waves" style="left: 0; transform: rotate3d(0,1,0,180deg);">
                <path class="shape-fill shape-fill-contrast"
                      d="M790.5,93.1c-59.3-5.3-116.8-18-192.6-50c-29.6-12.7-76.9-31-100.5-35.9c-23.6-4.9-52.6-7.8-75.5-5.3c-10.2,1.1-22.6,1.4-50.1,7.4c-27.2,6.3-58.2,16.6-79.4,24.7c-41.3,15.9-94.9,21.9-134,22.6C72,58.2,0,25.8,0,25.8V100h1000V65.3c0,0-51.5,19.4-106.2,25.7C839.5,97,814.1,95.2,790.5,93.1z"/>
            </svg>
        </div>
        <div class="container overflow-hidden">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-contrast bold">Ultimate HTML template <span class="d-block light">for your awesome product</span>
                    </h1>
                    <p class="lead">Your amazing product deserves an outstanding way to show it. Dashcore will provide
                        you with a quality template no matter what your idea is about.</p>
                    <nav class="nav mt-5"><a href="#" class="me-3 btn btn btn-rounded btn-contrast"><i
                                class="fas fa-tag me-3"></i> Plans & pricing </a><a href="#"
                                                                                    class="btn btn-rounded btn-success text-contrast">Start
                            now</a></nav>
                </div>
            </div>
        </div>
    </header><!-- ./Perspective mockups -->
    <section class="perspective-mockups hidden-preload">
        <div class="tablet ipad landscape">
            <div class="screen"><img src="img/screens/tablet/1.png" alt="..."></div>
            <div class="button"></div>
        </div>
        <div class="phone-big iphone-x">
            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div>
            <div class="notch"></div>
        </div>
        <div class="phone-small iphone-x">
            <div class="screen"><img src="img/screens/app/2.png" alt="..."></div>
            <div class="notch"></div>
        </div>
        <div class="tablet ipad portrait">
            <div class="screen"><img src="img/screens/tablet/2.png" alt="..."></div>
            <div class="button"></div>
        </div>
    </section><!-- ./Lightweight HTML - text overview -->
@endsection
