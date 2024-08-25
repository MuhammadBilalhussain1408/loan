<html>
<head>
    <title>Receipt</title>
    <style>
        #invoice-POS {
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
            padding: 2mm;
            margin: 0 auto;
            width: 44mm;
            background: #FFF;
        }

        ::selection {
            background: #f31544;
            color: #FFF;
        }

        ::moz-selection {
            background: #f31544;
            color: #FFF;
        }

        h1 {
            font-size: 1.5em;
            color: #222;
        }

        h2 {
            font-size: .9em;
        }

        h3 {
            font-size: 1.2em;
            font-weight: 300;
            line-height: 2em;
        }

        p {
            font-size: .7em;
            color: #666;
            line-height: 1.2em;
        }

        #top, #mid, #bot { /* Targets all id with 'col-' */
            border-bottom: 1px solid #EEE;
        }

        #top {
            min-height: 40px;
        }

        #mid {
            min-height: 80px;
        }

        #details {
            min-height: 80px;
        }

        #bot {
            min-height: 50px;
        }

        #top .logo {
            /*float: left;*/
            height: 60px;
            width: 60px;
        }


        .info {
            display: block;
            /*float: left;*/
            margin-left: 0;
        }

        .title {
            float: right;
        }

        .title p {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            /*padding: 5 px 0 5 px 15 px;
            / / border: 1 px solid #EEE*/
        }

        .tabletitle {
            /* padding: 5 px;*/
            font-size: .5em;
            background: #EEE;
        }

        .service {
            border-bottom: 1px solid #EEE;
        }

        .item {
            width: 24mm;
        }

        .itemtext {
            font-size: .5em;
        }

        #legalcopy {
            margin-top: 5mm;
        }

    </style>
</head>
<body>
<div id="invoice-POS">

    <div id="top" style="text-align: center;">
        @if($logo=\Illuminate\Support\Facades\DB::connection('mysql')->table('settings')->where('setting_key','company_logo')->first()->setting_value)
            <div class="logo" style="margin-left: auto; margin-right: auto;">
                <img alt="logo" src="{{asset('storage/'.$logo)}}" height="60"/>
            </div>
        @endif
        <div class="info">
            <h2>{{\Illuminate\Support\Facades\DB::connection('mysql')->table('settings')->where('setting_key','company_name')->first()->setting_value}}</h2>
            <p>
                {{\Illuminate\Support\Facades\DB::connection('mysql')->table('settings')->where('setting_key','company_email')->first()->setting_value}}<br>
                {{\Illuminate\Support\Facades\DB::connection('mysql')->table('settings')->where('setting_key','company_tel')->first()->setting_value}}<br>
                {{\Illuminate\Support\Facades\DB::connection('mysql')->table('settings')->where('setting_key','company_address')->first()->setting_value}}<br>
            </p>
        </div><!--End Info-->
    </div><!--End InvoiceTop-->
    <div id="details">
        <div class="info">
            <p>
                Client : {{tenant('name')}}<br>
                Invoice # : {{$invoicePayment->invoice_reference}}<br>
                Payment # : {{$invoicePayment->id}}<br>
                Amount : {{number_format($invoicePayment->amount,2)}}<br>
                Currency: {{$invoicePayment->currency}}<br>
                Mode: {{$invoicePayment->payment_type}}<br>
                Payment Date : {{$invoicePayment->date}}<br>
                Print Time : {{date('Y-m-d H:i:s')}}<br>
            </p>
        </div>
    </div><!--End Invoice Mid-->

    <div id="bot">
        <hr>
        <div id="legalcopy">
            <p class="legal"><strong>Thank you for your business!</strong> Have a wonderful day</p>
        </div>

    </div><!--End InvoiceBot-->
</div><!--End Invoice-->
</body>
</html>
