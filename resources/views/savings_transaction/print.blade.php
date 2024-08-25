<html lang="en">
<head>
    <title>Transaction Details</title>
    <style>
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
            display: table;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .pull-right {
            float: right !important;
        }

        span {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div>
    @if(!empty($company_logo=\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
        <img src="{{asset('storage/'.$company_logo)}}" alt="logo"/>
    @endif
    <h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
</div>
<div>
    <table class="table  table-bordered table-hover table-striped" id="">
        <tbody>
        <tr>
            <td>ID</td>
            <td class="text-right">{{$transaction->id}}</td>
        </tr>
        <tr>
            <td>Type</td>
            <td class="text-right">
                {{$transaction->type->name??''}}
            </td>
        </tr>
        <tr>
            <td>Date</td>
            <td class="text-right">{{$transaction->submitted_on}}</td>
        </tr>
        <tr>
            <td>Amount</td>
            <td class="text-right">
                {{number_format($transaction->amount,$transaction->savings->decimals)}}
            </td>
        </tr>
        @if(!empty($transaction->paymentDetail))
            <tr>
                <td colspan="2">
                    <b>Payment Details</b>
                </td>
            </tr>

            <tr>
                <td>Payment Type</td>
                <td class="text-right">
                    @if(!empty($transaction->paymentDetail->payment_type))
                        {{$transaction->paymentDetail->payment_type->name}}
                    @endif
                </td>
            </tr>
            @if(!empty($transaction->paymentDetail->account_number))
                <tr>
                    <td>Account#</td>
                    <td class="text-right">
                        {{$transaction->paymentDetail->account_number}}
                    </td>
                </tr>
            @endif
            @if(!empty($transaction->paymentDetail->cheque_number))
                <tr>
                    <td>Cheque#</td>
                    <td class="text-right">
                        {{$transaction->paymentDetail->cheque_number}}
                    </td>
                </tr>
            @endif
            @if(!empty($transaction->paymentDetail->routing_code))
                <tr>
                    <td>Routing Code</td>
                    <td>
                        {{$transaction->paymentDetail->routing_code}}
                    </td>
                </tr>
            @endif
            @if(!empty($transaction->paymentDetail->receipt))
                <tr>
                    <td>Receipt#</td>
                    <td class="text-right">
                        {{$transaction->paymentDetail->receipt}}
                    </td>
                </tr>
            @endif
            @if(!empty($transaction->paymentDetail->bank_name))
                <tr>
                    <td>Bank</td>
                    <td class="text-right">
                        {{$transaction->paymentDetail->bank_name}}
                    </td>
                </tr>
            @endif
            @if(!empty($transaction->paymentDetail->description))
                <tr>
                    <td>Notes</td>
                    <td class="text-right">
                        {{$transaction->paymentDetail->description}}
                    </td>
                </tr>
            @endif
        @endif
        </tbody>
    </table>
</div>
</body>
<script>
    window.print();
</script>
</html>
