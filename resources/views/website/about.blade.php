@extends('website.layouts.master')
@section('title')
    About Us-Best Hospital and Surgery Management Software in Zimbabwe
@endsection
@section('meta')
    <meta name="description" content="The YoPractice team is one of the most diverse teams in healthcare. We bring extensive practical experience building companies, leading teams, developing products and serving customers really well.">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="About Us"/>
    <meta property="og:description" content="The YoPractice team is one of the most diverse teams in healthcare. We bring extensive practical experience building companies, leading teams, developing products and serving customers really well."/>
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
                    <h1 class="bold display-md-4 text-contrast mb-4">About Us</h1>
                    <p class="lead text-contrast">People on a Mission</p>
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
                <h2 class="bold">A happy, healthy team</h2>
                <p class="lead text-secondary">
                    The YoPractice team is one of the most diverse teams in healthcare. We bring
                    extensive practical experience building companies, leading teams, developing products and serving
                    customers really well.
                    Though we are not all artifacts of the healthcare industry, we are all
                    passionate
                    about using technology to improve it.
                </p>
                <p>
                    It is our job to bridge the gap between optimal patient care and technology transformation. We
                    understand the growing demand for fitting medical services and the significance of reforming this
                    industry according to the need of time. This is why our team has created YoPractice, a solution that
                    covers the end to end operations of healthcare facilities. The journey was challenging, and we had
                    to bring complete configuration management and internal process automation modifications to our
                    system. Our experts’ diligence helped us achieve our objective and create a solution that offered
                    extensive health care service automation.
                </p>
                <p>
                    Our clients inspire us to deliver bespoke solutions that exceed their expectations and help them
                    with their operations. Each day, we strive to come up with something new and better than what we
                    have created. We work to stay ahead of market trends and always ensure the latest and best are
                    always available in YoPractice. Get your comprehensive solution today!
                </p>
            </div>
        </div>
    </section>

@endsection
