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
            min-height: 100px;
        }

        #mid {
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

        .clientlogo {
            float: left;
            height: 60px;
            width: 60px;
            background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
            background-size: 60px 60px;
            border-radius: 50px;
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
        @if($logo=\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value)
            <div class="logo" style="margin-left: auto; margin-right: auto;">
                <img alt="logo" src="{{asset('storage/'.$logo)}}" height="60"/>
            </div>
        @endif
        <div class="info">
            <h2>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h2>
        </div><!--End Info-->
    </div><!--End InvoiceTop-->

    <div id="mid">
        <div class="info">
            <h2>Contact Info</h2>
            <p>
                Address : {{\App\Models\Setting::where('setting_key','company_address')->first()->setting_value}}<br>
                Email : {{\App\Models\Setting::where('setting_key','company_email')->first()->setting_value}}<br>
                Phone : {{\App\Models\Setting::where('setting_key','company_tel')->first()->setting_value}}<br>
            </p>
        </div>
    </div><!--End Invoice Mid-->

    <div id="bot">

        <div id="table">
            <table>
                <tr class="tabletitle">
                    <td class="item"><h2>Item</h2></td>
                    <td class="Hours"><h2>Qty</h2></td>
                    <td class="Rate"><h2>Sub Total</h2></td>
                </tr>

                <tr class="service">
                    <td class="tableitem"><p class="itemtext">Communication</p></td>
                    <td class="tableitem"><p class="itemtext">5</p></td>
                    <td class="tableitem"><p class="itemtext">$375.00</p></td>
                </tr>

                <tr class="service">
                    <td class="tableitem"><p class="itemtext">Asset Gathering</p></td>
                    <td class="tableitem"><p class="itemtext">3</p></td>
                    <td class="tableitem"><p class="itemtext">$225.00</p></td>
                </tr>

                <tr class="service">
                    <td class="tableitem"><p class="itemtext">Design Development</p></td>
                    <td class="tableitem"><p class="itemtext">5</p></td>
                    <td class="tableitem"><p class="itemtext">$375.00</p></td>
                </tr>

                <tr class="service">
                    <td class="tableitem"><p class="itemtext">Animation</p></td>
                    <td class="tableitem"><p class="itemtext">20</p></td>
                    <td class="tableitem"><p class="itemtext">$1500.00</p></td>
                </tr>

                <tr class="service">
                    <td class="tableitem"><p class="itemtext">Animation Revisions</p></td>
                    <td class="tableitem"><p class="itemtext">10</p></td>
                    <td class="tableitem"><p class="itemtext">$750.00</p></td>
                </tr>


                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>tax</h2></td>
                    <td class="payment"><h2>$419.25</h2></td>
                </tr>

                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>Total</h2></td>
                    <td class="payment"><h2>$3,644.25</h2></td>
                </tr>

            </table>
        </div><!--End Table-->

        <div id="legalcopy">
            <p class="legal"><strong>Thank you for your business!</strong> Payment is expected within 31 days; please
                process this invoice within that time. There will be a 5% interest charge per month on late invoices.
            </p>
        </div>

    </div><!--End InvoiceBot-->
</div><!--End Invoice-->
</body>
</html>
