@extends('website.layouts.master')
@section('title')
    Billing & Revenue Management
@endsection
@section('meta')
    <meta name="description" content="Track revenue generated from your health services to help manage your practice better.">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Billing & Revenue Management"/>
    <meta property="og:description" content="Track revenue generated from your health services to help manage your practice better."/>
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
                    <h1 class="bold display-md-4 text-contrast mb-4">Billing & Revenue Management</h1>
                    <p class="lead text-contrast">Online billing and revenue reports in your Virtual Practice</p>
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
                <h2 class="bold">Manage your practice revenue with greater ease</h2>
                <p class="lead text-secondary">Track revenue generated from your health services to help manage your
                    practice better.</p>
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
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div>
                            <span class="button"></span>
                        </div>
                        <div class="iphone-x front me-0">
                            <div class="screen shadow-box"><img src="img/screens/app/2.png" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold">Payment for services</h5>
                        <p class="lead">Create and define payments for all of the services that your practice offers,
                            such as, consultation charges, lab costs, follow-up visit costs or any specific
                            services.</p>
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
                        <h5 class="bold">Generate invoices online</h5>
                        <p class="lead">You can generate e-invoices and bills through the Virtual Practice for your
                            health services, for clinic or online consultations. Select a service you want your charge
                            patients for, generate a bill and share it with your patient who can pay for their
                            consultation online.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">
                    <div class="center-xy op-1">
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init"
                             style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div>
                            <span class="button"></span>
                        </div>
                        <div class="iphone-x front me-0">
                            <div class="screen shadow-box"><img src="img/screens/app/2.png" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
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
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div>
                            <span class="button"></span>
                        </div>
                        <div class="iphone-x front me-0">
                            <div class="screen shadow-box"><img src="img/screens/app/2.png" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold">Revenue management through reports</h5>
                        <p class="lead">View your practice's expense reports, for each health provider and for all of
                            your services to help you keep track of your practice revenue.</p>
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
                        <h5 class="bold">View and Export Billing Reports</h5>
                        <p class="lead">Access billing reports generated for patients availing various services such as
                            video or clinic consultations, monitoring, etc. View, export & download billing reports to
                            identify and gauge revenue generated in your practice.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">
                    <div class="center-xy op-1">
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init"
                             style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div>
                            <span class="button"></span>
                        </div>
                        <div class="iphone-x front me-0">
                            <div class="screen shadow-box"><img src="img/screens/app/2.png" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
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
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div>
                            <span class="button"></span>
                        </div>
                        <div class="iphone-x front me-0">
                            <div class="screen shadow-box"><img src="img/screens/app/2.png" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold">Claims Processing</h5>
                        <p class="lead">Claim processing for payments claiming from medical institutions, biometric
                            verification is available for auto verification of some medical aid card holders</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
