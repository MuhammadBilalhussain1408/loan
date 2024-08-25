<h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
<h3 class="text-center">Collection Sheet</h3>
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
        <th colspan="2">
            @if(!empty($results->first()) && !empty($loan_officer_id))
                Loan Officer:
                {{$results->first()->loan->loanOfficer->name??''}}
            @endif
        </th>

    </tr>
    <tr class="green-heading">
        <th>Loan Officer</th>
        <th>Branch</th>
        <th>Client</th>
        <th>Mobile</th>
        <th>Loan#</th>
        <th>Product</th>
        <th class="p-2 font-medium text-gray-500">Expected Maturity Date</th>
        <th class="p-2 font-medium text-gray-500">Repayment Date</th>
        <th class="p-2 font-medium text-gray-500">Expected Amount</th>
        <th class="p-2 font-medium text-gray-500">Total Due</th>
    </tr>
    </thead>
    <tbody>
    @foreach($results as $key)
        <tr>
            <td>{{ $key->loan->loanOfficer->name??'' }}</td>
            <td>{{ $key->loan->branch->name??'' }}</td>
            <td>
                {{$key->loan->client->name??''}}
            </td>
            <td>{{ $key->loan->client->mobile??'' }}</td>
            <td>{{ $key->loan_id }}</td>

            <td>{{ $key->loan->product->name??'' }}</td>
            <td>{{ $key->loan->expected_maturity_date??'' }}</td>
            <td>{{ $key->due_date }}</td>
            <td>{{ number_format( $key->total,2) }}</td>
            <td>{{ number_format( $key->total_due,2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="8"><b>Total</b></td>
        <td>{{number_format($results->sum('total'),2)}}</td>
        <td>{{number_format($results->sum('total_due'),2)}}</td>
    </tr>
    </tfoot>
</table>
