<h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
<h3 class="text-center">Disbursement</h3>
<table class="table table-bordered table-striped table-condensed table-hover">
    <thead>
    <tr>
        <th colspan="4">Start Date: {{$start_date}}</th>
        <th colspan="4">End Date: {{$end_date}}</th>
        <th colspan="6">
            @if(!empty($results->first()) && !empty($branch_id))
               Branch:

                {{$results->first()->branch->name??''}}
            @endif
        </th>
        <th colspan="10">

        </th>

    </tr>
    <tr class="text-left font-bold">

        <th class="p-2 font-medium text-gray-500 border" colspan="8"></th>
        <th class="p-2 font-medium text-gray-500 border" colspan="5">Expected</th>
        <th class="p-2 font-medium text-gray-500 border" colspan="5">Actual</th>
        <th class="p-2 font-medium text-gray-500 border" colspan="6"></th>
    </tr>
    <tr class="green-heading">
        <th class="p-2 font-medium text-gray-500 border">Loan Officer</th>
        <th class="p-2 font-medium text-gray-500 border">Branch</th>
        <th class="p-2 font-medium text-gray-500 border">Client</th>
        <th class="p-2 font-medium text-gray-500 border">Mobile</th>
        <th class="p-2 font-medium text-gray-500 border">Loan#</th>
        <th class="p-2 font-medium text-gray-500 border">Product</th>
        <th class="p-2 font-medium text-gray-500 border">Disbursed Date</th>
        <th class="p-2 font-medium text-gray-500 border">Maturity Date</th>
        <th class="p-2 font-medium text-gray-500 border">Principal</th>
        <th class="p-2 font-medium text-gray-500 border">Interest</th>
        <th class="p-2 font-medium text-gray-500 border">Fees</th>
        <th class="p-2 font-medium text-gray-500 border">Penalties</th>
        <th class="p-2 font-medium text-gray-500 border">Total</th>
        <th class="p-2 font-medium text-gray-500 border">Principal</th>
        <th class="p-2 font-medium text-gray-500 border">Interest</th>
        <th class="p-2 font-medium text-gray-500 border">Fees</th>
        <th class="p-2 font-medium text-gray-500 border">Penalties</th>
        <th class="p-2 font-medium text-gray-500 border">Total</th>
        <th class="p-2 font-medium text-gray-500 border">Fund</th>
        <th class="p-2 font-medium text-gray-500 border">Purpose</th>
        <th class="p-2 font-medium text-gray-500 border">Status</th>
        <th class="p-2 font-medium text-gray-500 border">Arrears Amount</th>
        <th class="p-2 font-medium text-gray-500 border">Days In Arrears</th>
        <th class="p-2 font-medium text-gray-500 border">Days Since Last Payment</th>
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
            <td>{{ number_format( $key->principal,2) }}</td>
            <td>{{ number_format( $key->interest_disbursed_derived-$key->interest_written_off_derived-$key->interest_waived_derived,2) }}</td>
            <td>{{ number_format( $key->fees_disbursed_derived-$key->fees_written_off_derived-$key->fees_waived_derived,2) }}</td>
            <td>{{ number_format( $key->penalties_disbursed_derived-$key->penalties_written_off_derived-$key->penalties_waived_derived,2) }}</td>
            <td>{{ number_format( $key->total_disbursed_derived-$key->total_written_off_derived-$key->total_waived_derived,2) }}</td>
            <td>{{ number_format( $key->principal_outstanding_derived,2) }}</td>
            <td>{{ number_format( $key->interest_outstanding_derived,2) }}</td>
            <td>{{ number_format( $key->fees_outstanding_derived,2) }}</td>
            <td>{{ number_format( $key->penalties_outstanding_derived,2) }}</td>
            <td>{{ number_format( $key->total_outstanding_derived,2) }}</td>
            <td>{{  $key->fund->name??'' }}</td>
            <td>{{  $key->purpose->name??'' }}</td>
            <td>
                @if($key->status=='active')
                    Active
                @endif
                @if($key->status=='closed')
                    Closed
                @endif
                @if($key->status=='written_off')
                    Written Off
                @endif
            </td>
            <td>{{ number_format($key->arrears_amount,2) }}</td>
            <td>{{ $key->arrears_days}}</td>
            <td>{{ $key->days_since_last_payment}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="8"><b>Total</b></td>
        <td>{{number_format($results->sum('principal'),2)}}</td>
        <td>{{number_format($results->sum('interest_disbursed_derived')-$results->sum('interest_written_off_derived')-$results->sum('interest_waived_derived'),2)}}</td>
        <td>{{number_format($results->sum('fees_disbursed_derived')-$results->sum('fees_written_off_derived')-$results->sum('fees_waived_derived'),2)}}</td>
        <td>{{number_format($results->sum('penalties_disbursed_derived')-$results->sum('penalties_written_off_derived')-$results->sum('penalties_waived_derived'),2)}}</td>
        <td>{{number_format($results->sum('total_disbursed_derived')-$results->sum('total_written_off_derived')-$results->sum('total_waived_derived'),2)}}</td>
        <td>{{number_format($results->sum('principal_outstanding_derived'),2)}}</td>
        <td>{{number_format($results->sum('interest_outstanding_derived'),2)}}</td>
        <td>{{number_format($results->sum('fees_outstanding_derived'),2)}}</td>
        <td>{{number_format($results->sum('penalties_outstanding_derived'),2)}}</td>
        <td>{{number_format($results->sum('total_outstanding_derived'),2)}}</td>
        <td colspan="3"></td>
        <td>{{number_format($results->sum('arrears_amount'),2)}}</td>
        <td colspan="2"></td>
    </tr>
    </tfoot>
</table>
