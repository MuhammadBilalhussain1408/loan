<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><![endif]-->
    @yield('meta')
    <meta charset="utf-8">
    <meta name=”robots” content="index, follow">
    <meta name="viewport" content="width=device-width,initial-scale=1"><!-- Place favicon.ico in the root directory -->
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Caveat" rel="stylesheet">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('website/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/cookieconsent.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/odometer-theme-minimal.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/prism-okaidia.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/smart_wizard_all.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/swiper-bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/dashcore.css') }}">
    <!-- endinject -->
    {!! \App\Models\Setting::where('setting_key','google_analytics_code')->first()->setting_value !!}
<!-- Begin Inspectlet Asynchronous Code -->
    <script type="text/javascript">
        (function() {
            window.__insp = window.__insp || [];
            __insp.push(['wid', 1836230169]);
            var ldinsp = function(){
                if(typeof window.__inspld != "undefined") return; window.__inspld = 1; var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js?wid=1836230169&r=' + Math.floor(new Date().getTime()/3600000); var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
            setTimeout(ldinsp, 0);
        })();
    </script>
    <!-- End Inspectlet Asynchronous Code -->
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/626003d37b967b11798b9fc9/1g13gtsv5';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
</head>

<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->
<!-- ./Making stripe menu navigation -->
<nav class="st-nav navbar main-nav navigation fixed-top  dark-link navbar-sticky" id="main-nav">
    <div class="container">
        <ul class="st-nav-menu nav navbar-nav">
            <li class="st-nav-section nav-item">
                <a href="{{url('/')}}" class="navbar-brand">
                    <img src="{{asset('website/img/logo.png')}}" alt="YoPractice"
                         class="logo logo-sticky d-inline-block d-md-none">
                    <img src="{{asset('website/img/logo.png')}}" alt="YoPractice"
                         class="logo d-none d-md-inline-block">
                </a>
            </li>
            <li class="st-nav-section st-nav-primary nav-item">
                <a class="st-root-link nav-link" href="{{route('website.home')}}">Home</a>
                <a class="st-root-link  nav-link" href="{{route('website.features')}}">Features</a>
                <a class="st-root-link nav-link" href="{{route('website.pricing')}}">Pricing</a>
                <a class="st-root-link nav-link" href="{{route('website.on_premise')}}">On-Premise</a>
                <a class="st-root-link nav-link" href="{{route('website.blog')}}">Blog</a>
                <a class="st-root-link nav-link" href="{{route('website.about')}}">About</a>
                <a class="st-root-link nav-link" href="{{route('website.contact')}}">Contact Us</a>
            </li>
            <li class="st-nav-section st-nav-secondary nav-item">
                <a class="btn btn-rounded btn-solid px-3" href="{{route('website.signup')}}">
                    <i class="fas fa-user-plus d-none d-md-inline me-md-0 me-lg-2"></i>
                    <span class="d-md-none d-lg-inline">Start Free Trial</span>
                </a>
            </li><!-- Mobile Navigation -->
            <li class="st-nav-section st-nav-mobile nav-item">
                <button class="st-root-link navbar-toggler" type="button"><span class="icon-bar"></span> <span
                        class="icon-bar"></span> <span class="icon-bar"></span></button>
                <div class="st-popup">
                    <div class="st-popup-container"><a class="st-popup-close-button">Close</a>
                        <div class="st-dropdown-content-group">
                            <a class="regular" href="{{route('website.home')}}">Home</a>
                            <a class="regular" href="{{route('website.features')}}">Features</a>
                            <a class="regular" href="{{route('website.pricing')}}">Pricing</a>
                            <a class="regular" href="{{route('website.on_premise')}}">On-Premise</a>
                            <a class="regular" href="{{route('website.blog')}}">Blog</a>
                            <a class="regular" href="{{route('website.about')}}">About</a>
                            <a class="regular" href="{{route('website.contact')}}">Contact Us</a>

                        </div>
                        <div class="st-dropdown-content-group bg-light b-t">
                            <a href="{{route('website.signup')}}">Start Free Trial
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="st-dropdown-root">
        <div class="st-dropdown-bg">
            <div class="st-alt-bg"></div>
        </div>
        <div class="st-dropdown-arrow"></div>
        <div class="st-dropdown-container">
            <div class="st-dropdown-section" data-dropdown="blocks">
                <div class="st-dropdown-content">
                </div>
            </div>
        </div>
    </div>
</nav>
<main class="position-relative overflow-hidden">
    @yield('content')
    <section
        class="section gradient overlay alpha-8 gradient-purple-blue image-background cover text-contrast block bg-contrast"
        style="background-image: url(https://picsum.photos/350/200/?random&amp;gravity=south)">
        <div class="container py-5 py-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="text-contrast">Sign up for <span class="bold">Free</span> Trial</h2>
                    <p>With <span class="bold">YoPractice</span> you can transform your Healthcare business today in a
                        few easy steps</p>
                    <p>No credit card required</p>
                </div>
                <div class="col-md-4 ms-md-auto">
                    <p class="handwritten highlight font-md mb-4">Why wait?</p>
                    <a href="{{route('website.signup')}}" class="btn btn-contrast btn-rounded ms-3">Try it for Free
                        NOW!</a>
                </div>
            </div>
        </div>
    </section>
    <footer class="site-footer section b-t">
        <div class="container pb-3">
            <div class="row gap-y text-center text-md-start">
                <div class="col-md-4 me-auto"><img src="{{asset('website/img/logo.png')}}" alt="" class="logo">
                    <p>Practice better when everything works together</p>
                </div>
                <div class="col-md-2">
                    <h6 class="py-2 bold text-uppercase">Company</h6>
                    <nav class="nav flex-column">
                        <a class="nav-item py-2" href="{{route('website.about')}}">About</a>
                        <a class="nav-item py-2" href="{{route('website.privacy')}}">Privacy</a>
                    </nav>
                </div>
                <div class="col-md-2">
                    <h6 class="py-2 bold text-uppercase">Product</h6>
                    <nav class="nav flex-column">
                        <a class="nav-item py-2" href="{{route('website.features')}}">Features</a>
                        <a class="nav-item py-2" href="{{route('website.pricing')}}">Pricing</a>
                        <a class="nav-item py-2" href="{{route('website.signup')}}">Signup</a>
                    </nav>
                </div>
                <div class="col-md-2">
                    <h6 class="py-2 bold text-uppercase">Channels</h6>
                    <nav class="nav flex-column">
                        <a class="nav-item py-2" href="{{route('website.blog')}}">Blog</a>
                        <a class="nav-item py-2" href="{{route('website.contact')}}">Contact</a>
                    </nav>
                </div>
            </div>
            <hr class="mt-5">
            <div class="row small align-items-center">
                <div class="col-md-4">
                    <p class="mt-2 mb-md-0 text-secondary text-center text-md-start">© {{date('Y')}} YoPractice. All
                        Rights
                        Reserved</p>
                </div>
                <div class=" col-md-8">
                    <nav class="nav justify-content-center justify-content-md-end"><a href="#"
                                                                                      class="btn btn-circle btn-sm btn-secondary me-3 op-4"><i
                                class="fab fa-facebook"></i></a> <a href="#"
                                                                    class="btn btn-circle btn-sm btn-secondary me-3 op-4"><i
                                class="fab fa-twitter"></i></a> <a href="#"
                                                                   class="btn btn-circle btn-sm btn-secondary op-4"><i
                                class="fab fa-instagram"></i></a></nav>
                </div>
            </div>
        </div>
    </footer>
</main>
</body>
<script src="{{ asset('website/js/jquery.js') }}"></script>
<script src="{{ asset('website/js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('website/js/card.js') }}"></script>
<script src="{{ asset('website/js/counterup2.js') }}"></script>
<script src="{{ asset('website/js/noise.js') }}"></script>
<script src="{{ asset('website/js/noframework.waypoints.js') }}"></script>
<script src="{{ asset('website/js/odometer.js') }}"></script>
<script src="{{ asset('website/js/prism.js') }}"></script>
<script src="{{ asset('website/js/simplebar.js') }}"></script>
<script src="{{ asset('website/js/swiper-bundle.js') }}"></script>
<script src="{{ asset('website/js/jquery.easing.js') }}"></script>
<script src="{{ asset('website/js/jquery.validate.js') }}"></script>
<script src="{{ asset('website/js/jquery.smartWizard.js') }}"></script>
<script src="{{ asset('website/js/feather.js') }}"></script>
<script src="{{ asset('website/js/aos.js') }}"></script>
<script src="{{ asset('website/js/typed.js') }}"></script>
<script src="{{ asset('website/js/jquery.magnific-popup.js') }}"></script>
<script src="{{ asset('website/js/cookieconsent.js') }}"></script>
<script src="{{ asset('website/js/jquery.animatebar.js') }}"></script>
<script src="{{ asset('website/js/common.js') }}"></script>
<script src="{{ asset('website/js/stripe-bubbles.js') }}"></script>
<script src="{{ asset('website/js/stripe-menu.js') }}"></script>
<script src="{{ asset('website/js/credit-card.js') }}"></script>
<script src="{{ asset('website/js/pricing.js') }}"></script>
<script src="{{ asset('website/js/svg.js') }}"></script>
<script src="{{ asset('website/js/site.js') }}"></script>
<script src="{{ asset('website/js/wizards.js') }}"></script>
<script src="{{ asset('website/js/cookie-consent-util.js') }}"></script>
<script src="{{ asset('website/js/cookie-consent-themes.js') }}"></script>
<script src="{{ asset('website/js/cookie-consent-custom-css.js') }}"></script>
<script src="{{ asset('website/js/cookie-consent-informational.js') }}"></script>
<script src="{{ asset('website/js/cookie-consent-opt-out.js') }}"></script>
<script src="{{ asset('website/js/cookie-consent-opt-in.js') }}"></script>
<script src="{{ asset('website/js/cookie-consent-location.js') }}"></script>
<script src="{{ asset('website/js/demo.js') }}"></script>
</html>
