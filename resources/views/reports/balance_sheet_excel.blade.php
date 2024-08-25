<div class="header">
    <h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
    <h3 class="text-center">{{\App\Models\Report::where('symbol', 'j22')->first()->name}} As at {{$end_date}}</h3>
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
        </thead>
        <tbody>
        <tr class="bg-gray-50">
            <td class="border-t  font-bold px-6 py-4" colspan="2">
                Assets
            </td>
        </tr>
        @foreach($results['assets'] as $result)
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
                <strong>Total Assets</strong>
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong><span>{{number_format($results['assets']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        <tr class="bg-gray-50">
            <td class="border-t  font-bold px-6 py-4" colspan="2">
                Liabilities & Shareholders Equity
            </td>
        </tr>
        <tr class="">
            <td class="border-t  font-mediums px-6 py-4" colspan="2">
                Liabilities
            </td>
        </tr>
        @foreach($results['liabilities'] as $result)
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
                Total Liabilities
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <span>{{number_format($results['liabilities']->sum('balance'),2)}}</span>
            </td>
        </tr>
        <tr class="">
            <td class="border-t  font-mediums px-6 py-4" colspan="2">
                Shareholders Equity
            </td>
        </tr>
        @foreach($results['equity'] as $result)
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
                Total Shareholders Equity
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <span>{{number_format($results['equity']->sum('balance'),2)}}</span>
            </td>
        </tr>
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong>Total Liabilities & Shareholders Equity</strong>
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong>
                    <span>{{number_format($results['equity']->sum('balance')+$results['liabilities']->sum('balance'),2)}}</span></strong>
            </td>
        </tr>
        </tbody>
    </table>
</div>
