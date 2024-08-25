<div class="header">
    <h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
    <h3 class="text-center">Trial Balance</h3>
</div>
<div class="clearfix">
    <table class="table">
        <thead class="green-heading">
        <tr>
            <th colspan="1">
                As at  {{$end_date}}
            </th>
            <th colspan="1">
                Branch: {{$branch_id?(\App\Models\Branch::find($branch_id)->name??''):'All'}}
            </th>
            <th colspan="1">
                Currency: {{$currency_id?(\App\Models\Currency::find($currency_id)->name??''):'All'}}
            </th>
        </tr>
        <tr class="text-left font-bold">
            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Account</th>
            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Debit</th>
            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Credit</th>
        </tr>
        </thead>
        <tbody>
        @foreach($results as $result)
            <tr
                class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t px-6 py-4 ">
                    <span>{{$result->name}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->debit,2)}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->credit,2)}}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr
            class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t px-6 py-4">
                <span>Total</span>
            </td>
            <td class="border-t px-6 py-4 ">
                <span>{{number_format($results->sum('debit'),2)}}</span>
            </td>
            <td class="border-t px-6 py-4 ">
                <span>{{number_format($results->sum('credit'),2)}}</span>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
