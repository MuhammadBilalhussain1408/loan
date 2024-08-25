@extends('website.layouts.master')
@section('title')
    Pricing- Simple and affordable pricing plans for the best surgery and hospital management software in zimbabwe
@endsection
@section('meta')
    <meta name="description"
          content="Simple and affordable pricing plans. One Software solution with everything you need">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Pricing"/>
    <meta property="og:description"
          content="Simple and affordable pricing plans. One Software solution with everything you need"/>
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
                    <h1 class="bold display-md-4 text-contrast mb-4">Pricing plans</h1>
                    <p class="lead text-contrast">Simple and affordable pricing plans. One Software solution with
                        everything you need</p>
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
    </div><!-- ./Pricing Table - Simple Columns -->
    <section class="">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mt-5">
                    <div class="card text-center">
                        <div class="pricing card-header p-5 bg-light d-flex align-items-center flex-column">
                            <h4 class="bold">Basic</h4>
                            <div class="pricing-value"><span class="price text-dark">15</span></div>
                            <p>For Individual Practitioners. </p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> 2 Users</li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> 1 Branch</li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Appointments
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-danger"></i> Patient Portal
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-danger"></i> SMS
                                Notifications
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-danger"></i> Custom Web and
                                Email domain
                            </li>
                        </ul>
                        <div class="card-body">
                            <a href="{{url(route('website.signup',['name'=>'basic']))}}"
                               class="btn btn-rounded btn-outline-primary">
                                Start 30 day Free Trial
                                <i class="fa fa-long-arrow-alt-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-5">
                    <div class="card text-center">
                        <div
                            class="pricing card-header p-5 bg-primary text-contrast d-flex align-items-center flex-column">
                            <h4 class="bold text-contrast">Standard</h4>
                            <div class="pricing-value"><span class="price text-contrast">30</span></div>
                            <p>For larger Teams</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> 5 Users</li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> 2 Branches
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Appointments
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Patient Portal
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> SMS
                                Notifications
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-danger"></i> Custom Web and
                                Email domain
                            </li>

                        </ul>
                        <div class="card-body">
                            <a href="{{url(route('website.signup',['name'=>'standard']))}}"
                               class="btn btn-rounded btn-outline-primary">
                                Start 30 day Free Trial
                                <i class="fa fa-long-arrow-alt-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-5">
                    <div class="card text-center">
                        <div class="pricing card-header p-5 bg-light d-flex align-items-center flex-column">
                            <h4 class="bold">Business</h4>
                            <div class="pricing-value"><span class="price text-dark">100</span></div>
                            <p>For larger Teams</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Unlimited
                                Users
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Unlimited
                                Branches
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Appointments
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Patient Portal
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> SMS
                                Notifications
                            </li>
                            <li class="list-group-item"><i class="far fa-check-circle  text-success"></i> Custom Web and
                                Email domain
                            </li>

                        </ul>
                        <div class="card-body">
                            <a href="{{url(route('website.signup',['name'=>'business']))}}"
                               class="btn btn-rounded btn-outline-primary">
                                Start 30 day Free Trial
                                <i class="fa fa-long-arrow-alt-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- ./Plans features -->
    <section>
        <div class="container">
            <div class="section-heading text-center">
                <h2 class="bold">All our plans include</h2>
            </div>
            <div class="row gap-y text-center text-md-left">
                <div class="col-md-4"><i data-feather="headphones" width="36" height="36"
                                         class="stroke-info me-2 m-0"></i>
                    <h5 class="bold my-3">First class support</h5>
                    <p class="my-0">We are available 24/7 via phone or chat</p>
                </div>
                <div class="col-md-4"><i data-feather="box" width="36" height="36" class="stroke-info me-2 m-0"></i>
                    <h5 class="bold my-3">Automatic Backups</h5>
                    <p class="my-0">Backups are performed daily</p>
                </div>
                <div class="col-md-4"><i data-feather="headphones" width="36" height="36"
                                         class="stroke-info me-2 m-0"></i>
                    <h5 class="bold my-3">Full documentation</h5>
                    <p class="my-0">We have a large pool of resource to guide you</p>
                </div>
            </div>
        </div>
    </section><!-- ./FAQs -->
    <section class="section bg-light ">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h2>Do you have <span class="bold">questions?</span></h2>
                    <p class="text-muted">Here are the answers to some of the most common questions we hear from our
                        appreciated customers</p>
                </div>
                <div class="col-md-8">
                    <div class="accordion accordion-clean" id="faqs-accordion">
                        <div class="card mb-3">
                            <div class="card-header"><a href="#" class="card-title btn" data-bs-toggle="collapse"
                                                        data-bs-target="#v1-q1"><i class="fas fa-angle-down angle"></i>
                                    How do I sign up?</a></div>
                            <div id="v1-q1" class="collapse show" data-bs-parent="#faqs-accordion">
                                <div class="card-body">
                                    <p class="card-text">Sign up for your Virtual Practice account <a href="/pricing"
                                                                                                      class="link_btn nuxt-link-active">here</a>.
                                        You can try out the features of our platform for a 30-day trial period, before
                                        subscribing to a plan of your choice. We recommend that you upgrade your account
                                        before the trial is expired.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header">
                                <a href="#" class="card-title btn collapsed"
                                   data-bs-toggle="collapse" data-bs-target="#v1-q2"><i
                                        class="fas fa-angle-down angle"></i>What happens after the trial ends?
                                </a>
                            </div>
                            <div id="v1-q2" class="collapse" data-bs-parent="#faqs-accordion">
                                <div class="card-body">
                                    <p class="card-text"> Once your trial period ends, your account will be deactivated
                                        for 30 days, after which it will be terminated completely. You can login and
                                        subscribe to a plan within this duration to resume services.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header"><a href="#" class="card-title btn collapsed"
                                                        data-bs-toggle="collapse" data-bs-target="#v1-q3"><i
                                        class="fas fa-angle-down angle"></i> How can I pay for my Virtual Practice?</a>
                            </div>
                            <div id="v1-q3" class="collapse" data-bs-parent="#faqs-accordion">
                                <div class="card-body">
                                    <p class="card-text">You can choose a billing option (eg., annual, semi-annual
                                        billing) and pay for a subscription plan of your choice by credit or debit card.
                                        To pay for a subscription plan, access Settings &gt; Virtual Practice
                                        Subscription &gt; Virtual Practice Plans on your Virtual Practice Manager.
                                        Subscription payments can be done before or after the expiry of your free
                                        trial.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header"><a href="#" class="card-title btn collapsed"
                                                        data-bs-toggle="collapse" data-bs-target="#v1-q4"><i
                                        class="fas fa-angle-down angle"></i> Can I upgrade or downgrade my subscription?
                                </a></div>
                            <div id="v1-q4" class="collapse" data-bs-parent="#faqs-accordion">
                                <div class="card-body">
                                    <p class="card-text"> Yes, you can upgrade or downgrade your subscription, any time.
                                        Note that there will be no refunds on downgrading your subscription. In case you
                                        are downgrading, additional credits will be available for the payment made
                                        towards your current billing cycle, which will be utilized for payment of the
                                        new subscription plan. In case you are upgrading, you will be billed immediately
                                        for the difference in pricing of your current plan and new plan, till the next
                                        billing. </p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header"><a href="#" class="card-title btn collapsed"
                                                        data-bs-toggle="collapse" data-bs-target="#v1-q5"><i
                                        class="fas fa-angle-down angle"></i> Who are Team users? Do I pay for each Team
                                    user?
                                </a></div>
                            <div id="v1-q5" class="collapse" data-bs-parent="#faqs-accordion">
                                <div class="card-body">
                                    <p class="card-text">Team users refer to anyone you can add to your Virtual Practice
                                        team and they would generally be the healthcare providers, nurses,
                                        administrators, etc. of your organization. They can then individually login to
                                        your Virtual Practice Manager under their own accounts, to provide and manage
                                        services. Each plan supports a number of team users and you can choose one that
                                        meets your requirements. On the other hand, patient users are unlimited and free
                                        on all plans.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header"><a href="#" class="card-title btn collapsed"
                                                        data-bs-toggle="collapse" data-bs-target="#v1-q6"><i
                                        class="fas fa-angle-down angle"></i> What payment methods are accepted?
                                </a></div>
                            <div id="v1-q6" class="collapse" data-bs-parent="#faqs-accordion">
                                <div class="card-body">
                                    <p class="card-text">We accept credit and debit cards for subscription payments.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header"><a href="#" class="card-title btn collapsed"
                                                        data-bs-toggle="collapse" data-bs-target="#v1-q7"><i
                                        class="fas fa-angle-down angle"></i> I want to use my own domain instead of the
                                    YoPractice subdomain. Is that possible?
                                </a></div>
                            <div id="v1-q7" class="collapse" data-bs-parent="#faqs-accordion">
                                <div class="card-body">
                                    <p class="card-text">Yes, we do support mapping of a custom domain to the patient
                                        portal website which allows you to use your own domain e.g.
                                        www.yourcompanyname.com instead of yourcompanyname.yopractice.com. Once you
                                        have taken up a paid subscription, contact our support team and we will guide
                                        you through the process. For more information on mapping your own domain to your
                                        patient portal, <a
                                            href=""
                                            class="link_btn">click here.</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
