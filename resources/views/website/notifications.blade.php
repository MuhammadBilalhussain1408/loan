@extends('website.layouts.master')
@section('title')
    Notifications-Improve communication with your patients
@endsection
@section('meta')
    <meta name="description" content="Improve communication with your patients by sending automated alerts and instant notifications on your Virtual Practice">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Notifications-Improve communication with your patients"/>
    <meta property="og:description" content="Improve communication with your patients by sending automated alerts and instant notifications on your Virtual Practice"/>
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
                    <h1 class="bold display-md-4 text-contrast mb-4">Improve communication with your patients</h1>
                    <p class="lead text-contrast">Automated alerts and instant notifications on your Virtual
                        Practice</p>
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
                <h2 class="bold">Notifications in the Virtual Practice</h2>
                <p class="lead text-secondary">Whether about new services, events or simply holiday reminder, let
                    patients know about what's happening with easy patient notifications.</p>
            </div>
        </div>
    </section>
    <section class="section overflow-hidden mb-4 mt-4">
        <div class="container bring-to-front">
            <div class="row gap-y align-items-center">
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">

                    <div class="device-twin mx-auto align-items-center">
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen">
                                <img src="{{asset('website/img/pn_icon.png')}}" alt="...">
                            </div>
                            <span class="button"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold"><i class="far fa-check-circle  text-danger"></i> Marketing communication</h5>
                        <h5 class="bold"><i class="far fa-check-circle  text-danger"></i> Alerts to improve adherence
                        </h5>
                        <h5 class="bold"><i class="far fa-check-circle  text-danger"></i> Automated reminders</h5>
                        <h5 class="bold"><i class="far fa-check-circle  text-danger"></i> Patient-specific notifications
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
