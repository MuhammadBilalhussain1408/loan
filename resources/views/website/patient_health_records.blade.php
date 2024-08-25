@extends('website.layouts.master')
@section('title')
    Patient Health Records|Patient health records Zimbabwe
@endsection
@section('meta')
    <meta name="description" content="Patient Health Records">
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
    <header class="page header text-contrast bg-primary" style="">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="bold display-md-4 text-contrast mb-4">Patient Health Records</h1>
                    <p class="lead text-contrast">Access patients' Personal Health Records at any time. Online Health
                        Records To Help You Make Better
                        Health Decisions</p>
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
                <h2 class="bold">Personal Health Records in the Virtual Practice</h2>
                <p class="lead text-secondary">Confidential, secure and easily accessible, Personal Health Records in
                    the Virtual Practice is a convenient way to access your patients' medical history</p>
            </div>
        </div>
    </section>
    <section class="section overflow-hidden mb-4 mt-4">
        <div class="container bring-to-front">
            <div class="row gap-y align-items-center">
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">
                    <div class="center-xy op-1">
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init"
                             style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="iphone-x front me-0" style="max-width: 100%">
                            <div class="screen shadow-box"><img src="{{asset('website/img/patientslist.png')}}" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold">Accessibility to health information</h5>
                        <p class="lead">Add patients or allow patients to register with your Virtual Practice. With just
                            a click you'll be able to access and manage their individual Personal Health Records (PHR)
                            during telemedicine sessions, text consultations or clinic consultations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section overflow-hidden mb-4 mt-4">
        <div class="container bring-to-front">
            <div class="row gap-y align-items-center">

                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold">Comprehensive health history</h5>
                        <p class="lead">The patient PHR provides a comprehensive medical history and health profile of
                            your patients. Unlike other EHR systems, patient PHR can be updated by your practice and by
                            patients to share health reports and medication information.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">
                    <div class="center-xy op-1">
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init"
                             style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="iphone-x front me-0"  style="max-width: 100%">
                            <div class="screen shadow-box"><img src="{{asset('website/img/doctor_diagnosis.png')}}" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
