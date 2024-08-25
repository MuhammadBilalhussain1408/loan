<h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
<h3 class="text-center">Repayments</h3>
<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th colspan="2">Start Date: {{$start_date}}</th>
        <th colspan="2">End Date: {{$end_date}}</th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($branch_id))
                Branch:

                {{$results->first()->loan->branch->name??''}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($loan_product_id))
                Product:

                {{$results->first()->loan->product->name??''}}
            @endif
        </th>
        <th colspan="3">
            @if(!empty($results->first()) && !empty($loan_officer_id))
                Loan Officer:
                {{$results->first()->loan->loanOfficer->name??''}}
            @endif
        </th>
    </tr>
    <tr class="green-heading">
        <th class="p-2 font-medium text-gray-500">ID</th>
        <th class="p-2 font-medium text-gray-500">Client</th>
        <th class="p-2 font-medium text-gray-500">Loan Officer</th>
        <th class="p-2 font-medium text-gray-500">Loan#</th>
        <th class="p-2 font-medium text-gray-500">Principal</th>
        <th class="p-2 font-medium text-gray-500">Interest</th>
        <th class="p-2 font-medium text-gray-500">Fees</th>
        <th class="p-2 font-medium text-gray-500">Penalties</th>
        <th class="p-2 font-medium text-gray-500">Total</th>
        <th class="p-2 font-medium text-gray-500">Date</th>
        <th class="p-2 font-medium text-gray-500"> Payment Type</th>
    </tr>
    </thead>
    <tbody>

    @foreach($results as $key)
        <tr>
            <td>{{ $key->id }}</td>
            <td>
                {{$key->loan->client->name??''}}
            </td>
            <td>{{ $key->loan->loanOfficer->name??'' }}</td>
            <td>{{ $key->loan_id }}</td>
            <td>{{ number_format( $key->principal_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->interest_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->fees_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->penalties_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->amount,2) }}</td>
            <td>{{ $key->submitted_on }}</td>
            <td>{{ $key->paymentDetail->payment_type->name??'' }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4"><b>Total</b></td>
        <td>{{number_format($results->sum('principal_repaid_derived'),2)}}</td>
        <td>{{number_format($results->sum('interest_repaid_derived'),2)}}</td>
        <td>{{number_format($results->sum('fees_repaid_derived'),2)}}</td>
        <td>{{number_format($results->sum('penalties_repaid_derived'),2)}}</td>
        <td>{{number_format($results->sum('amount'),2)}}</td>
        <td colspan="2"></td>
    </tr>
    </tfoot>
</table>
