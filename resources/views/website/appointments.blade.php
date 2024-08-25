@extends('website.layouts.master')
@section('title')
    Appointments
@endsection
@section('meta')
    <meta name="description" content="Manage your clinic and online appointments and consultations in one place. Allow staff to schedule appointments for patients or have patient book them online or on their mobile app.">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Appointments"/>
    <meta property="og:description" content="Manage your clinic and online appointments and consultations in one place. Allow staff to schedule appointments for patients or have patient book them online or on their mobile app."/>
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
                    <h1 class="bold display-md-4 text-contrast mb-4">Scheduling and Booking Appointments Are Now a Breeze</h1>
                    <p class="lead text-contrast">Helping your staff and patients book appointments to manage time effectively</p>
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
        <div class="section-heading text-center mt-4">
            <h2 class="bold">Appointments On Your Virtual Practice</h2>
            <p class="lead text-secondary">Manage your clinic and online appointments and consultations in one place. Allow staff to schedule appointments for patients or have patient book them online or on their mobile app.</p>
        </div>
    </section>
    <section class="section overflow-hidden mb-4 mt-4">
        <div class="container bring-to-front">
            <div class="row gap-y align-items-center">
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">
                    <div class="center-xy op-1">
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init" style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div><span class="button"></span>
                        </div>
                        <div class="iphone-x front me-0">
                            <div class="screen shadow-box"><img src="img/screens/app/2.png" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold">Set up consultation timings</h5>
                        <p class="lead">Set up available days and timings that your practice would be accepting appointment requests. This way you can effectively manage time and also have the opportunity to balance your online and clinic consultations.</p>
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
                        <h5 class="bold">Manage Your Appointment Calendar</h5>
                        <p class="lead">Your can confirm or cancel appointment requests or even reschedule upcoming appointments based. Your patients will be notified of their appointment status automatically and your appointment calendar will indicate the staus of all of your appointments.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">
                    <div class="center-xy op-1">
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init" style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div><span class="button"></span>
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
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init" style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div><span class="button"></span>
                        </div>
                        <div class="iphone-x front me-0">
                            <div class="screen shadow-box"><img src="img/screens/app/2.png" alt="..."></div>
                            <div class="notch"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <div class="section-heading">
                        <h5 class="bold">Get Automated Reminders</h5>
                        <p class="lead">Both you and your patients will receive automated reminders about confirmed upcoming appointments. This allows you to plan your day and prevents patient no-shows.</p>
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
                        <h5 class="bold">Easy Appointment Booking For Patients</h5>
                        <p class="lead">In addition to staff scheduling appointments for patients, your patients can book appointments online or on their mobile app with your practice, without having to call your practice. Confirm, reschedule or cancel patient requests based on your schedule.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 me-lg-auto position-relative">
                    <div class="center-xy op-1">
                        <div class="shape shape-background rounded-circle shadow-lg bg-info aos-init" style="width: 600px; height: 600px;" data-aos="zoom-in"></div>
                    </div>
                    <div class="device-twin mx-auto align-items-center">
                        <div class="mockup absolute aos-init aos-animate" data-aos="fade-left">
                            <div class="screen"><img src="img/screens/app/1.png" alt="..."></div><span class="button"></span>
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

@endsection
