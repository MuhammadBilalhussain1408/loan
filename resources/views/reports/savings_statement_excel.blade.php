<h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
<h3 class="text-center">Savings Account Statement</h3>
<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th colspan="2">Start Date: {{$start_date}}</th>
        <th colspan="2">End Date: {{$end_date}}</th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($branch_id))
                Branch:

                {{$results->first()->savings->branch->name??''}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($savings_product_id))
                Product:

                {{$results->first()->savings->product->name??''}}
            @endif
        </th>
        <th colspan="3">
            @if(!empty($results->first()) && !empty($savings_officer_id))
                Savings Officer:
                {{$results->first()->savings->savingsOfficer->name??''}}
            @endif
        </th>
    </tr>
    <tr class="green-heading">
        <th class="p-2 font-medium text-gray-500">ID</th>
        <th class="p-2 font-medium text-gray-500">Client</th>
        <th class="p-2 font-medium text-gray-500">Savings Officer</th>
        <th class="p-2 font-medium text-gray-500">Savings#</th>
        <th class="p-2 font-medium text-gray-500">Type</th>
        <th class="p-2 font-medium text-gray-500">Debit</th>
        <th class="p-2 font-medium text-gray-500">Credit</th>
        <th class="p-2 font-medium text-gray-500">Balance</th>
        <th class="p-2 font-medium text-gray-500">Date</th>
        <th class="p-2 font-medium text-gray-500">Receipt</th>
        <th class="p-2 font-medium text-gray-500">Payment Type</th>
    </tr>
    </thead>
    <tbody>

    @foreach($results as $key)
        <tr>
            <td>{{ $key->id }}</td>
            <td>
                {{$key->savings->client->name??''}}
            </td>
            <td>{{ $key->savings->savingsOfficer->name??'' }}</td>
            <td>{{ $key->savings_id }}</td>
            <td>{{ $key->type->name??'' }}</td>
            <td>{{ number_format( $key->debit,2) }}</td>
            <td>{{ number_format( $key->credit,2) }}</td>
            <td>{{ number_format( $key->balance,2) }}</td>
            <td>{{ $key->submitted_on }}</td>
            <td>{{ $key->paymentDetail->reciept??'' }}</td>
            <td>{{ $key->paymentDetail->payment_type->name??'' }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5"><b>Total</b></td>
        <td>{{number_format($results->sum('debit'),2)}}</td>
        <td>{{number_format($results->sum('credit'),2)}}</td>
        <td>{{number_format($results->sum('balance'),2)}}</td>
        <td colspan="3"></td>
    </tr>
    </tfoot>
</table>
