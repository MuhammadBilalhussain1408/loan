@extends('website.layouts.master')
@section('title')
    On-Premise-Install the best hospital surgery management software on your server
@endsection
@section('meta')
    <meta name="description" content="Install a Powerful Practice Management System on your own Server">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="On-Premise"/>
    <meta property="og:description" content="Install a Powerful Practice Management System on your own Server"/>
    <meta property="og:url" content="{{request()->fullUrl()}}"/>
    <meta property="og:site_name" content="YoPractice"/>
    <meta property="og:image" content="{{asset('website/img/logo.png')}}"/>
@endsection
@section('content')
    <!-- ./Page header -->
    <header class="page header text-contrast bg-primary" style="">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="bold display-md-4 text-contrast mb-4">On-Premise Software</h1>
                    <p class="lead text-contrast">YoPractice on your server</p>
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
    <section>
        <div class="container py-5 py-4">
            <div class="section-heading text-center mt-4">
                <h2 class="bold">Want to run YoPractice on your server?</h2>
                <p class="lead text-secondary">Install a Powerful Practice Management System on your own Server</p>
                <a href="{{route('website.contact')}}"
                   class="btn btn-primary btn-rounded mt-3 mt-md-0 ms-md-auto">Contact Us</a>
            </div>
        </div>
    </section>
@endsection
