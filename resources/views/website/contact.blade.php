@extends('website.layouts.master')
@section('title')
     Contact Us-Best Hospital Management Software Zimbabwe|Best Surgery Management Software Zimbabwe
@endsection
@section('meta')
    <meta name="description" content="Contact Us">
    <meta name="keywords" content="car repair zimbabwe,car repair harare, best car repair harare, auto harare, auto zimbabwe, auto repair,best auto repair harare">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Ignition"/>
    <meta property="og:description" content="We are your one stop shop for all your motor vehicle servicing and repair solutions in Zimbabwe. All our mechanics are certified car repair specialists."/>
    <meta property="og:url" content="{{request()->fullUrl()}}"/>
    <meta property="og:site_name" content="Ignition"/>
    <meta property="og:image" content="{{asset('website/img/logo.png')}}"/>
@endsection
@section('content')
    <header class="page header text-contrast bg-primary" style="">
        <div class="container pb-9">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="bold display-md-4 text-contrast mb-4">Contact us</h1>
                    <p class="lead text-contrast">Get in touch and let us know how we can help. Fill out the form and
                        we’ll be in touch as soon as possible.</p>
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
    </div><!-- ./Contact Us -->
    <section class="section mt-n7">
        <div class="container bring-to-front pt-0">
            <div class="row align-items-center gap-y">
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{session('success')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                    @if(session()->has('error'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{session('error')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="col-md-6">
                    <div class="shadow bg-contrast p-4 rounded">
                        <form action="{{route('website.contact')}}" method="post" class="form form-contact"
                              name="form-contact"
                              data-response-message-animation="slide-in-up">
                            @csrf
                            <x-honey/>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="first_name" class="text-dark bold mb-0">First Name</label>
                                        <input type="text" name="first_name" id="first_name"
                                               class="form-control bg-contrast" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="last_name" class="text-dark bold mb-0">Last Name</label>
                                        <input type="text" name="last_name" id="last_name"
                                               class="form-control bg-contrast" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="company" class="text-dark bold">Company Name</label>
                                <input
                                    type="text" name="company" id="company"
                                    class="form-control bg-contrast" required>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="text-dark bold mb-0">Email
                                    address</label>
                                <div id="emailHelp" class="small form-text text-secondary mt-0 mb-2 italic">
                                    We'll never
                                    share your email with anyone else.
                                </div>
                                <input type="email" name="email" id="email"
                                       class="form-control bg-contrast" placeholder="Valid Email" required>
                            </div>
                            <div class="mb-4">
                                <label for="phone" class="text-dark bold mb-0">Phone</label>
                                <input type="text" name="phone" id="phone"
                                       class="form-control bg-contrast"
                                       placeholder="Phone number with country code(+263)" required>
                            </div>
                            <div class="mb-4">
                                <label for="country" class="text-dark bold mb-0">Country</label>
                                <select type="text" name="country" id="country"
                                        class="form-select bg-contrast">
                                    @foreach($countries as $key)
                                        <option value="{{$key->name}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="message" class="text-dark bold">Message</label>
                                <textarea name="message" id="message" class="form-control bg-contrast"
                                          placeholder="Tell us in detail about your organization's requirements, for us to assist you."
                                          rows="8" required></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button data-loading-text="Sending..." name="submit" value="send" type="submit"
                                        class="btn btn-primary btn-rounded">Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-5 ms-md-auto">
                    <div class="d-flex mt-md-5"><i class="fas fa-map-marker font-l text-primary me-3"></i>
                        <div class="flex-fill">Harare,<span class="d-block">Zimbabwe</span></div>
                    </div>
                    <div class="d-flex my-4"><i class="fas fa-phone font-l text-primary me-3"></i>
                        <div class="flex-fill"><span class="d-block"><a
                                    href="tel:+1-123-456-7890">(263) 719175438</a></span> <span class="d-block"><a
                                    href="tel:+1-987-654-3201">(263) 773442092</a></span></div>
                    </div>
                    <div class="d-flex"><i class="fas fa-envelope font-l text-primary me-3"></i>
                        <div class="flex-fill"><a href="mailto:info@yopractice.com">info@yopractice.com</a></div>
                    </div>
                    <hr class="my-4">
                    <nav class="nav justify-content-center justify-content-md-start"><a href="#"
                                                                                        class="btn btn-circle btn-secondary btn-sm me-3"><i
                                class="fab fa-facebook"></i></a> <a href="#"
                                                                    class="btn btn-circle btn-secondary btn-sm me-3"><i
                                class="fab fa-twitter"></i></a> <a href="#" class="btn btn-circle btn-secondary btn-sm"><i
                                class="fab fa-instagram"></i></a></nav>
                </div>
            </div>
        </div>
    </section><!-- ./Other contact channels -->
    <section class="section b-b">
        <div class="container">
            <div class="row gap-y align-items-center text-center text-lg-start">
                <div class="col-12 col-md-6 py-4 px-5 b-md-r"><i data-feather="dollar-sign" width="36" height="36"
                                                                 class="stroke-darker"></i> <a href="javascript:;"
                                                                                               class="mt-4 text-darker d-flex align-items-center">
                        <h4 class="me-3">Contact Sales</h4><i class="fas fa-long-arrow-alt-right"></i>
                    </a>
                    <p class="mt-4">Looking for a custom quote? need to tell us more about your practice? or want a
                        demonstration? drop us a line to <a href="mailto:yopractice.com">sales@yopractice.com</a>
                    </p>
                </div>
                <div class="col-12 col-md-6 py-4 px-5"><i data-feather="life-buoy" width="36" height="36"
                                                          class="stroke-darker"></i> <a href="javascript:;"
                                                                                        class="mt-4 text-darker d-flex align-items-center">
                        <h4 class="me-3">Technical Support</h4><i class="fas fa-long-arrow-alt-right"></i>
                    </a>
                    <p class="mt-4">Any question about how the product works?. Don't fret, our geek team is
                        ready for you at <a href="mailto:support@yopractice.com">support@yopractice.com</a></p>
                </div>
            </div>
        </div>
    </section>
@endsection
