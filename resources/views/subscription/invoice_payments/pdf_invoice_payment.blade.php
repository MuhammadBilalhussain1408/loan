<html>
<head>
    <title>{{ __('general.Receipt') }} #{{ $invoice_payment->id }}</title>
    <style>
        body {
            font-family: "Helvetica Neue", Helvetica, sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
        }

        .invoice-preview {
            max-width: 900px;
            min-width: 400px;
            margin: auto;
        }

        .invoice-preview table {
            width: 100%;
            margin-top: 40px;
        }

        .invoice-preview td,
        .invoice-preview th {
            padding: 10px;
        }

        .invoice-preview * {
            line-height: 20px !important;
        }


        .ribbon {
            position: absolute;
            left: -35px;
            top: -1px;
            z-index: 1;
            overflow: hidden;
            width: 110px;
            height: 110px;
            text-align: right;
        }

        .ribbon span {
            font-size: 12px;
            font-weight: bold;
            color: #FFF;
            text-align: center;
            line-height: 20px;
            transform: rotate(-45deg);
            -webkit-transform: rotate(-45deg);
            width: 147px;
            display: block;
            position: absolute;
            top: 35px;
            left: -28px;
            padding: 0 !important;
        }

        .ribbon span::before {
            content: "";
            position: absolute;
            left: 0px;
            top: 100%;
            z-index: -1;
            border-left: 3px solid #aaa;
            border-right: 3px solid transparent;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #aaa;
        }

        .ribbon span::after {
            content: "";
            position: absolute;
            right: 0px;
            top: 100%;
            z-index: -1;
            border-left: 3px solid transparent;
            border-right: 3px solid #aaa;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #aaa;
        }

        .invoice-meta {
            font-size: 100% !important;
        }

        .print-invoice .invoice-preview-container {
            border: 1px solid #dadada;
        }

        .print-invoice .ribbon span::before, .print-invoice .ribbon span::after {
            border: none !important;
        }

        .print-invoice .ribbon {
            width: 105px;
            height: 105px;
            left: -30px;
            top: -30px;
        }

        .print-invoice .table-responsive, .print-invoice .table-responsive tr th, .print-invoice .table-responsive tr td:not([colspan='3']) {
            border: 1px solid #eee !important;
        }

        .print-invoice .invoice-pdf-hidden-table {
            border: none !important;
        }

        .print-invoice td.invoice-header-style-one .invoice-info-title {
            margin-right: -5px !important;
        }

        .print-invoice td.invoice-header-style-two .invoice-info-title {
            margin-left: -5px !important;
        }
    </style>
</head>
<body>
<div class="invoice-preview">
    <div class="invoice-preview-container bg-white mt15">
        <div class="col-md-12">
            <table style="color: #444; width: 100%;">
                <tbody>
                <tr class="invoice-preview-header-row">
                    <td style="width: 45%; vertical-align: top;">
                        @if(!empty($logo=\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                            <img src="{{public_path('storage/uploads/'.$logo)}}">
                        @endif
                    </td>
                    <td class="hidden-invoice-preview-row" style="width: 20%;"></td>
                    <td class="invoice-info-container invoice-header-style-one"
                        style="width: 35%; vertical-align: top; text-align: right">
                        <div style="line-height: 10px;"></div>
                        <span>#: {{$invoice_payment->id}}</span><br>
                        <span>
                             {{ __('general.Payment Method') }}:
                            @if(!empty($invoice_payment->payment_detail->payment_type))
                              {{$invoice_payment->payment_detail->payment_type->name}}
                            @endif
                        </span><br>
                        <span>
                            {{ __('general.Date') }} :
                             {{$invoice_payment->date}}
                        </span><br>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div><b>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</b></div>
                        <div style="line-height: 3px;"></div>
                        <span class="invoice-meta" style="font-size: 90%; color: #666;">
                                        {{\App\Models\Setting::where('setting_key','company_address')->first()->setting_value}}
                                        <br>
                                        {{__('general.Phone')}}: {{\App\Models\Setting::where('setting_key','company_phone')->first()->setting_value}}
                                        <br>
                                        {{__('general.Website')}}: <a
                                    style="color:#666; text-decoration: none;"
                                    href="{{\App\Models\Setting::where('setting_key','company_website')->first()->setting_value}}">{{\App\Models\Setting::where('setting_key','company_website')->first()->setting_value}}</a>
                                    </span>
                    </td>
                    <td></td>
                    <td>

                        <div style="line-height: 2px; border-bottom: 1px solid #f2f2f2;"></div>
                        <div style="line-height: 3px;"></div>
                        @if(!empty($invoice_payment->invoice->tenant))
                            <strong>{{$invoice_payment->invoice->tenant->name}} </strong>
                        @endif

                        <div style="line-height: 3px;"></div>
                        <span class="invoice-meta" style="font-size: 90%; color: #666;">
                                        <div>
                                            {{$invoice_payment->invoice->tenant->address}}
                                            <br>
                                            @if(!empty($invoice_payment->invoice->tenant->country))
                                                {{$invoice_payment->invoice->tenant->country->name}}
                                            @endif
                                        </div>
                                    </span>
                    </td>
                </tr>
                </tbody>
            </table>

            <div style="clear: both"></div>
            <div style="text-align: center; font-size: 15px;">
                <b>{{__('general.Payment For')}}</b>
            </div>
            <br>
            <table class="table-responsive" style="width: 100%; color: #444;">
                <tbody>
                <tr style="font-weight: bold; background-color: #2AA384; color: #fff;  ">
                    <th style="border-right: 1px solid #eee;"> {{__('general.Invoice')}} #</th>
                    <th style="text-align: center;   border-right: 1px solid #eee;"> {{__('general.Invoice')}} {{__('general.Date')}}</th>
                    <th style="text-align: right;  border-right: 1px solid #eee;"> {{__('general.Invoice')}} {{__('general.Amount')}}</th>
                    <th style="text-align: right;  border-right: 1px solid #eee;"> {{__('general.Payment')}}  {{__('general.Amount')}}</th>
                    <th style="text-align: right;"> {{__('general.Balance')}}</th>
                </tr>
                <tr style="background-color: #f4f4f4; ">
                    <td style="border: 1px solid #fff; padding: 10px;">
                        {{$invoice_payment->invoice->reference}}
                    </td>
                    <td style="text-align: center; width: 15%; border: 1px solid #fff;"> {{$invoice_payment->invoice->bill_date}}</td>
                    <td style="text-align: center; width: 15%; border: 1px solid #fff;"> {{number_format($invoice_payment->invoice->amount,2)}}</td>
                    <td style="text-align: right; width: 20%; border: 1px solid #fff;"> {{number_format($invoice_payment->amount,2)}}</td>
                    <td style="text-align: right; width: 20%; border: 1px solid #fff; color: red"> {{number_format($invoice_payment->invoice->balance,2)}}</td>
                </tr>


                </tbody>
            </table>
            <br>
            <table class="invoice-pdf-hidden-table"
                   style="border-top: 2px solid #f2f2f2; margin: 0; padding: 0; display: block; width: 100%; height: 10px;"></table>
            <span style="color:#444; line-height: 14px;"><p><br></p></span>
        </div>
    </div>
</body>
</html>
