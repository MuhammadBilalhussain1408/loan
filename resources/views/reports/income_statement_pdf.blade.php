<style>
    body {
        font-size: 9px;
    }

    .table {
        width: 100%;
        border: 1px solid #ccc;
        border-collapse: collapse;
    }

    .table th, td {
        padding: 5px;
        text-align: left;
        border: 1px solid #ccc;
        font-size: 9px;
    }

    .bg-gray {
        background-color: grey;
    }

    .light-heading th {
        background-color: #eeeeee
    }

    .green-heading th {
        background-color: #4CAF50;
        color: white;
    }

    .text-center {
        text-align: center;
    }

    .table-striped tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .text-danger {
        color: #a94442;
    }

    .text-success {
        color: #3c763d;
    }

    .header {
        text-align: center;
    }

    .title {
        font-weight: bold;
    }

    .title-content {
        margin-left: 10px;
    }

    .clearfix {
        clear: both;
    }

    * {
        overflow: auto
    }
</style>
<div class="header">
    <h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
    <h3 class="text-center">Income Statement From {{$start_date}}
        to {{$end_date}}</h3>
</div>
<div class="clearfix">
    <table class="table">
        <thead class="green-heading">
        <tr>
            <th colspan="1">
                Branch: {{$branch_id?(\App\Models\Branch::find($branch_id)->name??''):'All'}}
            </th>
            <th colspan="1">
                Currency: {{$currency_id?(\App\Models\Currency::find($currency_id)->name??''):'All'}}
            </th>
        </tr>
        <tr>
            <th colspan="1">
                Account
            </th>
            <th colspan="1">
                Total
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="bg-gray-50">
            <td class="border-t  font-bold px-6 py-4" colspan="2">
                Operating Income
            </td>
        </tr>
        @foreach($results['income'] as $result)
            <tr
                class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t px-6 py-4 ">
                    <span>{{$result->name}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->balance,2)}}</span>
                </td>
            </tr>
        @endforeach
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong>Total Operating Income</strong>
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong><span>{{number_format($results['income']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        <tr class="bg-gray-50">
            <td class="border-t  font-bold px-6 py-4" colspan="2">
                Cost of Goods Sold
            </td>
        </tr>
        @foreach($results['expenses'] as $result)
            <tr
                class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t px-6 py-4 ">
                    <span>{{$result->name}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->balance,2)}}</span>
                </td>
            </tr>
        @endforeach
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                Total Cost of Goods Sold
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong><span>{{number_format($results['expenses']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                Gross Profit
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong><span>{{number_format($results['income']->sum('balance')-$results['expenses']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        <tr class="">
            <td class="border-t  font-mediums px-6 py-4" colspan="2">
                Operating Expense
            </td>
        </tr>
        @foreach($results['other_expenses'] as $result)
            <tr
                class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t px-6 py-4 ">
                    <span>{{$result->name}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->balance,2)}}</span>
                </td>
            </tr>
        @endforeach
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                Total Operating Expense
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong> <span>{{number_format($results['other_expenses']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong> Operating Profit</strong>
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong>
                    <span>{{number_format($results['income']->sum('balance')-$results['expenses']->sum('balance')-$results['other_expenses']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        <tr class="">
            <td class="border-t  font-mediums px-6 py-4" colspan="2">
                Non Operating Income
            </td>
        </tr>
        @foreach($results['other_income'] as $result)
            <tr
                class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t px-6 py-4 ">
                    <span>{{$result->name}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->balance,2)}}</span>
                </td>
            </tr>
        @endforeach
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                Total Non Operating Income
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong><span>{{number_format($results['other_income']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                Net Profit/Loss
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong>
                    <span>{{number_format($results['income']->sum('balance')+$results['other_income']->sum('balance')-$results['expenses']->sum('balance')-$results['other_expenses']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        </tbody>
    </table>
</div>
