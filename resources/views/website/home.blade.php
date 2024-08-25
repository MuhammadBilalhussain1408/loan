@extends('website.layouts.master')
@section('title')
    YoPractice - Medical Practice Management Software and Patient health records Zimbabwe|Best Hospital and Surgery Management Software in Zimbabwe
@endsection
@section('meta')
    <meta name="description"
          content="YoPractice is a State-of-the-art All-In-One Medical Practice Management, Hospital Management, Telehealth and Patient Engagement Software. We have everything you need to manage your medical practice(surgery) or hospital. We have the best hospital and surgery management software in Zimbabwe.">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="YoPractice - Medical Practice Management Software"/>
    <meta property="og:description"
          content="YoPractice is a State-of-the-art All-In-One Medical Practice Management, Hospital Management, Telehealth and Patient Engagement Software. We have everything you need to manage your medical practice(surgery) or hospital. We have the best hospital and surgery management software in Zimbabwe."/>
    <meta property="og:url" content="{{request()->fullUrl()}}"/>
    <meta property="og:site_name" content="YoPractice"/>
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
                    <h1 class="text-contrast bold">All-In-One Practice Management Software
                    </h1>
                    <p class="lead">Everything you need to manage your medical practice</p>
                    <nav class="nav mt-5">
                        <a href="{{route('website.pricing')}}" class="me-3 btn btn btn-rounded btn-contrast"><i
                                class="fas fa-tag me-3"></i> Plans & pricing </a>
                        <a href="{{route('website.signup')}}" class="btn btn-rounded btn-success text-contrast">
                            Free 30 Day Trial
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header><!-- ./Perspective mockups -->
    <section class="perspective-mockups hidden-preload">
        <div class="tablet ipad landscape">
            <div class="screen"><img src="{{asset('website/img/dashboard.png')}}" alt="..."></div>
            <div class="button"></div>
        </div>
        <div class="phone-big iphone-x">
            <div class="screen"><img src="{{asset('website/img/dashboard.png')}}" alt="..."></div>
            <div class="notch"></div>
        </div>
        <div class="phone-small iphone-x">
            <div class="screen"><img src="{{asset('website/img/dashboard.png')}}" alt="..."></div>
            <div class="notch"></div>
        </div>
        <div class="tablet ipad portrait">
            <div class="screen"><img src="{{asset('website/img/dashboard.png')}}" alt="..."></div>
            <div class="button"></div>
        </div>
    </section><!-- ./Lightweight HTML - text overview -->
    <section id="" class="section">
        <div class="container">
            <div class="row gap-y text-center text-md-start">
                <div class="col-md-4 py-4 text-center position-relative">

                    <figure class="mockup mb-4 ">
                        <img src="{{asset('website/img/cloud-icon.png')}}" class="mb-3 img-step  mx-auto d-block">
                    </figure>
                    <h5 class="bold">Secure and Compliant</h5>
                    <p class="text-muted">A safe and secure cloud solution. HIPAA and GDPR compliant</p>
                </div>
                <div class="col-md-4 py-4 text-center position-relative">

                    <figure class="mockup  mb-4">
                        <img src="{{asset('website/img/easy-platform.png')}}" class="mb-3 img-step  mx-auto d-block">
                    </figure>
                    <h5 class="bold">The only software you need</h5>
                    <p class="text-muted">Telemedicine, Practice Management, Patient Portal, Website & Apps</p>
                </div>
                <div class="col-md-4 py-4 text-center position-relative">

                    <figure class="mockup mb-4">
                        <img src="{{asset('website/img/revenue-icon.png')}}" class="mb-3 img-step mx-auto d-block">
                    </figure>
                    <h5 class="bold">Grow your telemedicine revenues</h5>
                    <p class="text-muted">Practice telemedicine your way with video calls, text consultations and care
                        plans</p>
                </div>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="section-heading text-center">
                <h2 class="bold">Everything you need for
                    your practice</h2>
            </div>
            <nav
                class="slide nav nav-tabs nav-outlined nav-rounded justify-content-around justify-content-md-center mb-5">
                <a class="nav-item nav-link active" data-bs-toggle="tab" href="#patient-health-records">Patient Health
                    Records</a>
                <a class="nav-item nav-link " data-bs-toggle="tab" href="#billing">Billing & Reports</a>
                <a class="nav-item nav-link" data-bs-toggle="tab" href="#appointments">Appointments</a>
                <a class="nav-item nav-link" data-bs-toggle="tab" href="#notifications">Notifications</a>
                <a class="nav-item nav-link" data-bs-toggle="tab" href="#patient-portal">Patient Portal</a>
            </nav>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="patient-health-records">
                    <div class="row gap-y align-items-center">
                        <div class="col-md-6 me-md-auto">
                            <div class="browser shadow aos-init aos-animate" data-aos="fade-end"><img
                                    src="{{asset('website/img/patientprofileconsultations.png')}}"
                                    class="img-responsive" alt=""></div>
                        </div>
                        <div class="col-md-5">
                            <h2 class="display-4 light">Health Records</h2>
                            <p>The right information leads to better decisions. Make informed decisions about your
                                patients' care with access to their health records. Comprehensive health records of your
                                patients that are accessible to you during consultations.</p>
                            <p><a href="{{route('website.patient_health_records')}}"
                                  class="btn btn-primary btn-rounded mt-3 mt-md-0 ms-md-auto">Learn More</a></p>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="billing">
                    <div class="row gap-y align-items-center">
                        <div class="col-md-6 order-md-2 ms-md-auto">
                            <div class="browser shadow aos-init aos-animate" data-aos="fade-start">
                                <img
                                    src="{{asset('website/img/invoices.png')}}" alt="" class="img-responsive">
                            </div>
                        </div>
                        <div class="col-md-5 me-md-auto">
                            <h2 class="display-4 light">Billing & Reports</h2>
                            <p>Keep track of your growing practice. Collect payments online and manage online billing
                                and revenue reports for your practice to track revenue. An easy option to audit your
                                practice, helping you identify what services works best for your practice</p>
                            <p><a href="{{route('website.patient_billing')}}"
                                  class="btn btn-primary btn-rounded mt-3 mt-md-0 ms-md-auto">Learn More</a></p>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="appointments">
                    <div class="row gap-y align-items-center">
                        <div class="col-md-6 me-md-auto position-relative">
                            <div class="browser shadow-box aos-init aos-animate" data-aos="fade-end"><img
                                    src="{{asset('website/img/appointments_calendar_view.png')}}" alt=""
                                    class="img-responsive"></div>
                            <img src="img/screens/dash/2.1.png" alt="" class="absolute img-responsive shadow rounded"
                                 style="right: 0; top: 0">
                        </div>
                        <div class="col-md-5">
                            <h2 class="display-4 light">Appointments</h2>
                            <p>Streamlining consultations, online and in-clinic. We help you keep track of your busy
                                schedule and manage your time. Appointment scheduling for staff and booking for patients
                                with status updates to prevent no-shows and save your practice losses.</p>
                            <p><a href="{{route('website.appointments')}}"
                                  class="btn btn-primary btn-rounded mt-3 mt-md-0 ms-md-auto">Learn More</a></p>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="notifications">
                    <div class="row gap-y align-items-center">
                        <div class="col-md-6 order-md-2 ms-md-auto">
                            <div class="browser shadow aos-init aos-animate" data-aos="fade-start"><img
                                    src="img/screens/dash/1.png" alt="" class="img-responsive"></div>
                        </div>
                        <div class="col-md-5">
                            <h2 class="display-4 light">Notifications</h2>
                            <p>Communication made simple. Tell patients about what's new and send communication about
                                events that may be relevant to them. Saves your staff time and reduces the need to
                                invest in different tools for email or SMS communication.</p>
                            <p><a href="{{route('website.notifications')}}"
                                  class="btn btn-primary btn-rounded mt-3 mt-md-0 ms-md-auto">Learn More</a></p>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="patient-portal">
                    <div class="row gap-y align-items-center">
                        <div class="col-md-6 me-md-auto position-relative">
                            <div class="browser shadow-box aos-init aos-animate" data-aos="fade-end"><img
                                    src="{{asset('website/img/patientportal.png')}}" alt="" class="img-responsive">
                            </div>
                            <img src="img/screens/dash/2.1.png" alt="" class="absolute img-responsive shadow rounded"
                                 style="right: 0; top: 0">
                        </div>
                        <div class="col-md-5">
                            <h2 class="display-4 light">Patient Portal</h2>
                            <p>Customize your online presence. A user-friendly patient portal to showcase your practice
                                and its services. Integrated with a health blog for patient education. Easy to edit at
                                any time, without any assistance.</p>
                            <p><a href="{{route('website.patient_portal')}}"
                                  class="btn btn-primary btn-rounded mt-3 mt-md-0 ms-md-auto">Learn More</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section bg-light">
        <div class="container">
            <h4 class="bold text-center mb-5">Part of our happy customers</h4>
        </div>
    </section><!-- ./Testimonials -->
    <section class="section bg-light">
        <div class="container bring-to-front py-0">
            <div class="shadow bg-contrast p-5 rounded">
                <div class="testimonials-slider swiper-center-nav">
                    <div class="swiper-container pb-5">
                        <div class="swiper-wrapper text-center w-50">
                            <div class="swiper-slide">
                                <div class="d-flex flex-column align-items-center"><img src="img/avatar/1.jpg" alt=""
                                                                                        class="rounded-circle shadow mb-4">
                                    <p class="w-75 lead mt-3">YoPractice makes digitalizing medical records a breeze.
                                        The best feature though is their great support team!</p>
                                    <hr class="w-50">
                                    <footer><cite class="bold text-primary text-capitalize">— Dr S Mazude,</cite> <span
                                            class="small text-secondary mt-0">Medical Practice</span></footer>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="d-flex flex-column align-items-center"><img src="img/avatar/2.jpg" alt=""
                                                                                        class="rounded-circle shadow mb-4">
                                    <p class="w-75 lead mt-3">An ideal fusion of technology and science that has
                                        interfaced to deliver quality health services and address the existing lacunae
                                        to assist the medical community thus creating the current state of art in mobile
                                        health.</p>
                                    <hr class="w-50">
                                    <footer><cite class="bold text-primary text-capitalize">— Dr E Tavashure,</cite>
                                        <span
                                            class="small text-secondary mt-0">Medical</span></footer>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="d-flex flex-column align-items-center"><img src="img/avatar/3.jpg" alt=""
                                                                                        class="rounded-circle shadow mb-4">
                                    <p class="w-75 lead mt-3">Allowing doctors to offer telemedicine, remote monitoring
                                        health plans, collect payments and the health blog are some of the best features
                                        of </p>
                                    <hr class="w-50">
                                    <footer><cite class="bold text-primary text-capitalize">— Prof Mujuru,</cite> <span
                                            class="small text-secondary mt-0">Medical</span></footer>
                                </div>
                            </div>

                        </div><!-- Add Pagination -->
                        <div class="swiper-pagination"></div>
                    </div><!-- Prev button -->
                    <div class="swiper-button swiper-button-prev nav-testimonial-prev rounded-circle shadow"><i
                            data-feather="arrow-left"></i></div><!-- Next button -->
                    <div class="swiper-button swiper-button-next nav-testimonial-next rounded-circle shadow"><i
                            data-feather="arrow-right"></i></div>
                </div>
            </div>
        </div>
    </section>

@endsection
