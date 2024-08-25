<h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
<h3 class="text-center">Arrears</h3>
<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($branch_id))
                Branch:

                {{$results->first()->branch->name??''}}
            @endif
        </th>
        <th colspan="12">

        </th>
        <th colspan="2"></th>
        <th colspan="3">As At: {{$end_date}}</th>
    </tr>
    <tr class="green-heading">
        <th class="p-2 font-medium text-gray-500">Loan Officer</th>
        <th class="p-2 font-medium text-gray-500">Branch</th>
        <th class="p-2 font-medium text-gray-500">Client</th>
        <th class="p-2 font-medium text-gray-500">Mobile</th>
        <th class="p-2 font-medium text-gray-500">Loan#</th>
        <th class="p-2 font-medium text-gray-500">Product</th>
        <th class="p-2 font-medium text-gray-500">Disbursed Date</th>
        <th class="p-2 font-medium text-gray-500">Maturity Date</th>
        <th class="p-2 font-medium text-gray-500">Remaining</th>
        <th class="p-2 font-medium text-gray-500">Amount</th>
        <th class="p-2 font-medium text-gray-500">Principal</th>
        <th class="p-2 font-medium text-gray-500">Interest</th>
        <th class="p-2 font-medium text-gray-500">Fees</th>
        <th class="p-2 font-medium text-gray-500">Penalties</th>
        <th class="p-2 font-medium text-gray-500">Total</th>
        <th class="p-2 font-medium text-gray-500">Outstanding</th>
        <th class="p-2 font-medium text-gray-500">%</th>
        <th class="p-2 font-medium text-gray-500">Days In Arrears</th>
        <th class="p-2 font-medium text-gray-500">Days Since Last Payment</th>
    </tr>
    </thead>
    <tbody>
    @foreach($results as $key)
        <tr>
            <td>{{ $key->loanOfficer->name??'' }}</td>
            <td>{{ $key->branch->name??'' }}</td>
            <td>
                {{$key->client->name??''}}
            </td>
            <td>{{ $key->client->mobile??'' }}</td>
            <td>{{ $key->id }}</td>
            <td>{{ $key->product->name??'' }}</td>
            <td>{{ $key->disbursed_on_date }}</td>
            <td>{{ $key->expected_maturity_date }}</td>
            <td>{{ $key->remaining_days }}</td>
            <td>{{ number_format( $key->principal,2) }}</td>
            <td>{{ number_format( $key->principal_overdue,2) }}</td>
            <td>{{ number_format( $key->interest_overdue,2) }}</td>
            <td>{{ number_format( $key->fees_overdue,2) }}</td>
            <td>{{ number_format( $key->penalties_overdue,2) }}</td>
            <td>{{ number_format( $key->arrears_amount,2) }}</td>
            <td>{{ number_format( $key->total_outstanding_derived,2) }}</td>
            <td>{{$key->percentage_overdue}}</td>
            <td>{{$key->arrears_days}}</td>
            <td>{{$key->days_since_last_payment}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="9"><b>Total</b></td>
        <td>{{number_format($results->sum('principal'),2)}}</td>
        <td>{{number_format($results->sum('principal_overdue'),2)}}</td>
        <td>{{number_format($results->sum('interest_overdue'),2)}}</td>
        <td>{{number_format($results->sum('fees_overdue'),2)}}</td>
        <td>{{number_format($results->sum('penalties_overdue'),2)}}</td>
        <td>{{number_format($results->sum('arrears_amount'),2)}}</td>
        <td>{{number_format($results->sum('total_outstanding_derived'),2)}}</td>
        <td colspan="3"></td>
    </tr>
    </tfoot>
</table>
