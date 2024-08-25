<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><![endif]-->
    @yield('meta')
    <meta name="viewport" content="width=device-width,initial-scale=1"><!-- Place favicon.ico in the root directory -->
    <meta name="keywords" content="car repair zimbabwe,car repair harare, best car repair harare, auto harare, auto zimbabwe, auto repair,best auto repair harare">
    <meta property="og:locale" content="en_US"/>
    <meta property="og:title" content="Ignition"/>
    <meta property="og:description" content="We are your one stop shop for all your motor vehicle servicing and repair solutions in Zimbabwe. All our mechanics are certified car repair specialists."/>
    <meta property="og:url" content="{{request()->fullUrl()}}"/>
    <meta property="og:site_name" content="Ignition"/>
    <meta property="og:image" content="{{asset('website/img/logo.png')}}"/>
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Caveat" rel="stylesheet">
    <title>Signup-Create your YoPractice Account</title>
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
        (function () {
            window.__insp = window.__insp || [];
            __insp.push(['wid', 1836230169]);
            var ldinsp = function () {
                if (typeof window.__inspld != "undefined") return;
                window.__inspld = 1;
                var insp = document.createElement('script');
                insp.type = 'text/javascript';
                insp.async = true;
                insp.id = "inspsync";
                insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js?wid=1836230169&r=' + Math.floor(new Date().getTime() / 3600000);
                var x = document.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(insp, x);
            };
            setTimeout(ldinsp, 0);
        })();
    </script>
    <!-- End Inspectlet Asynchronous Code -->
</head>

<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->
<!-- ./Making stripe menu navigation -->
<main id="app">
    <div class="container">
        <div style="width: 450px; margin-left: auto; margin-right: auto; margin-top: 50px">
            <div class="text-center">
                <h4>Create your Account</h4>
                <p><small>Complete engagement and better healthcare for your patients</small></p>
            </div>
            @if (count($errors) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade show  mt-2">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>
                                        <div class="alert-text">{{ $error }}</div>
                                    </li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif
            <form method="post" action="{{route('website.process_signup')}}">
                @csrf
                <x-honey/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label for="name" class="text-dark bold mb-0">Practice Name</label>
                            <input type="text" name="name" id="name"
                                   class="form-control bg-contrast @error('name') is-invalid @enderror"
                                   required="" @keyup="updateSubdomain" v-model="name">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label for="subdomain" class="text-dark bold mb-0">Subdomain</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">https://</span>
                                </div>
                                <input type="text"
                                       class="form-control bg-contrast @error('subdomain') is-invalid @enderror"
                                       placeholder="subdomain" name="subdomain"
                                       aria-label="subdomain" v-model="subdomain" aria-describedby="basic-addon2"
                                       id="subdomain">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">@yopractice.com</span>
                                </div>
                            </div>
                            @error('subdomain')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="first_name" class="text-dark bold mb-0">First Name</label>
                            <input type="text" name="first_name" id="first_name"
                                   class="form-control bg-contrast @error('first_name') is-invalid @enderror"
                                   required="" v-model="first_name">
                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="last_name" class="text-dark bold mb-0">Last Name</label>
                            <input type="text" name="last_name" id="last_name"
                                   class="form-control bg-contrast @error('last_name') is-invalid @enderror"
                                   required="" v-model="last_name">
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="country_id"
                                   class="text-dark bold mb-0">Country</label>
                            <select id="country_id"
                                    class="form-select @error('country_id') is-invalid @enderror"
                                    name="country_id" v-model="country_id" required
                                    autocomplete="country_id">
                                <option></option>
                                @foreach($countries as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="mobile" class="text-dark bold mb-0">Mobile</label>
                            <div class="input-group">
                                <select id="phone_code" style="padding-left: 1px;padding-right: 1px"
                                        class="form-select @error('phone_code') is-invalid @enderror"
                                        name="phone_code" v-model="phone_code" required
                                        autocomplete="phone_code">
                                    <option></option>
                                    @foreach($countries as $key)
                                        <option value="{{$key->phone_code}}">+{{$key->phone_code}}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="mobile" id="mobile"
                                       class="form-control bg-contrast @error('mobile') is-invalid @enderror"
                                       required="" v-model="mobile">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label for="email" class="text-dark bold mb-0">Email</label>
                            <input type="email" name="email" id="email"
                                   class="form-control bg-contrast @error('email') is-invalid @enderror"
                                   required="" v-model="email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="password" class="text-dark bold mb-0">Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control bg-contrast @error('password') is-invalid @enderror"
                                   required="" v-model="password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="password_confirmation" class="text-dark bold mb-0">Repeat Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control bg-contrast @error('password_confirmation') is-invalid @enderror"
                                   required="" v-model="password_confirmation">
                            @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="checkbox">
                    <input type="checkbox" name="terms" id="terms" class=" @error('terms') is-invalid @enderror" v-model="terms">
                    <label for="terms">
                        I Accept The
                        <a href="{{route('website.terms')}}" target="_blank" style="margin-left: 10px; margin-right: 10px"> Terms Of Service </a>
                        and
                        <a href="{{route('website.privacy')}}" target="_blank" style="margin-left: 10px"> Privacy Policy</a>
                    </label>
                    @error('terms')
                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                    @enderror
                </div>
                <div class="d-grid gap-2 mt-4">
                    <button id="signup_btn" data-loading-text="Sending..." name="submit" type="submit"
                            class="btn btn-primary  btn-lg btn-block" :disabled="!terms && !button_enabled">Create
                        Account
                    </button>
                </div>
            </form>
            <div class="row">
                <div class="col-md-12">

                </div>
            </div>
        </div>
    </div>
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
<script src="{{ asset('website/js/axios.min.js') }}"></script>
<script src="{{ asset('website/js/vue.min.js') }}"></script>
<script>
    var app = new Vue({
        el: "#app",
        data: {
            button_enabled: false,
            terms: false,
            first_name: '{{old('first_name')}}',
            last_name: '{{old('last_name')}}',
            name: '{{old('name')}}',
            email: '{{old('email')}}',
            phone_code: '{{old('phone_code')}}',
            mobile: '{{old('mobile')}}',
            country_id: '{{old('country_id')}}',
            timezone_id: '{{old('timezone_id')}}',
            subdomain: '{{old('subdomain')}}',
            password: '{{old('password')}}',
            password_confirmation: '{{old('password_confirmation')}}',
            countries: @json($countries),
        },
        methods: {
            updateSubdomain() {
                this.subdomain = this.name.replace(/ /g, '').toLocaleLowerCase();

            },
            searchSubdomain() {
                axios.post('{{route('website.search_subdomain')}}', {
                    subdomain: this.subdomain
                }).then(function (response) {
                    if (response.data.success == true) {
                        this.button_enabled = true;
                        $('#subdomain').removeClass('text-danger').addClass('text-success');
                    } else {
                        //toastr.warning(response.data.message);
                        this.button_enabled = false;
                        $('#subdomain').removeClass('text-success').addClass('text-danger');
                    }
                }).catch(function (error) {
                    this.button_enabled = false;
                    $('#subdomain').removeClass('text-success').addClass('text-danger');
                    //toastr.warning(error.data.message);
                })
            }
        },
        watch: {
            subdomain: function (newValue, oldValue) {
                this.searchSubdomain();
            },
            country_id: function (newValue, oldValue) {
                this.countries.forEach(item => {
                    if (newValue == item.id) {
                        this.phone_code = item.phone_code;
                    }
                });
            }
        }
    })
</script>
</html>
