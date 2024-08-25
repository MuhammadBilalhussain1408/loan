@extends('website.layouts.master')
@section('title')
    Patient Portal
@endsection
@section('meta')
    <meta name="description" content="Allow patients to access your services on your very own patient portal">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Patient Portal"/>
    <meta property="og:description" content="Allow patients to access your services on your very own patient portal"/>
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
                    <h1 class="bold display-md-4 text-contrast mb-4">Allow patients to access your services on your very
                        own patient portal</h1>
                    <p class="lead text-contrast">An online platform for patients to actively engage with your
                        practice</p>
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
                <h2 class="bold">The Virtual Practice Web Patient Portal</h2>
                <p class="lead text-secondary">The web patient portal is accessible to patients from your Virtual
                    Practice
                    website, for them to securely access all of the services and capabilities of your Virtual
                    Practice.</p>
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
                        <h5 class="bold">Website page for patient registrations</h5>
                        <p class="lead">Your web patient portal is accessible from your Virtual Practice website page
                            which showcases information about your practice, staff, displays patient testimonials and
                            also displays your health blog. Your patients can register with your Virtual Practice on
                            your website and access its services through the patient portal.
                            Make your website discoverable online with a custom domain address of your choice (eg.
                            www.drsmith.com).</p>
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
                        <h5 class="bold">Access patient health profile</h5>
                        <p class="lead">Once registered, your patients can visit the patient portal to access their
                            health profile to update Personal Health Records (PHR), view prescriptions, medical reports
                            and update details of their medical history.</p>
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
                        <h5 class="bold">Book Appointments</h5>
                        <p class="lead">Your practice location and consultations timings will be visible to patients on
                            your patient portal. Accordingly, they can book appointments with your practice for online
                            or clinic consultations and view the status of their appointments by logging in to the
                            patient portal.</p>
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
                        <h5 class="bold">View Prescriptions</h5>
                        <p class="lead">Details of existing or past e-prescriptions shared with your patients will be
                            visible to them when they log in to your Virtual Practice patient portal.</p>
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
                        <h5 class="bold">Make Payments</h5>
                        <p class="lead">Patients can view the invoices for your health services in the patient portal.
                            They can also make secure online payments for your Virtual Practice services.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
