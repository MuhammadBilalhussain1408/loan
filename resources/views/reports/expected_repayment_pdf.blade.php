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

</style>
<h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
<h3 class="text-center">Expected Repayments</h3>
<table class="table table-bordered table-condensed table-striped table-hover">
    <thead>
    <tr>
        <th colspan="3">Start Date: {{$start_date}}</th>
        <th colspan="3">End Date: {{$end_date}}</th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($branch_id))
                Branch:

                {{$results->first()->branch??''}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($loan_product_id))
                Product:

                {{$results->first()->loan_product??''}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($loan_officer_id))
                Loan Officer:
                {{$results->first()->loan_officer??''}}
            @endif
        </th>
    </tr>
    <tr class="text-left font-bold border-t">
        <th class="p-2 font-medium text-gray-500"></th>
        <th class="p-2 font-medium text-gray-500 border" colspan="5">Expected</th>
        <th class="p-2 font-medium text-gray-500 border" colspan="5">Actual</th>
        <th class="p-2 font-medium text-gray-500 border"></th>
    </tr>
    <tr class="text-left font-bold border-t">
        <th class="p-2 font-medium text-gray-500 border">Branch</th>
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
        <th class="p-2 font-medium text-gray-500 border">Balance</th>
    </tr>
    </thead>
    <tbody>
    @foreach($results as $key)
        <tr>
            <td>{{ $key->branch }}</td>
            <td>{{ number_format( $key->principal,2) }}</td>
            <td>{{ number_format( $key->interest,2) }}</td>
            <td>{{ number_format( $key->fees,2) }}</td>
            <td>{{ number_format( $key->penalties,2) }}</td>
            <td>{{ number_format( $key->total,2) }}</td>
            <td>{{ number_format( $key->principal_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->interest_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->fees_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->penalties_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->total-$key->balance,2) }}</td>
            <td>{{ number_format( $key->balance,2) }}</td>

        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th><b>Total</b></th>
        <th>{{number_format($results->sum('principal'),2)}}</th>
        <th>{{number_format($results->sum('interest'),2)}}</th>
        <th>{{number_format($results->sum('fees'),2)}}</th>
        <th>{{number_format($results->sum('penalties'),2)}}</th>
        <th>{{number_format($results->sum('total'),2)}}</th>
        <th>{{number_format($results->sum('principal_repaid_derived'),2)}}</th>
        <th>{{number_format($results->sum('interest_repaid_derived'),2)}}</th>
        <th>{{number_format($results->sum('fees_repaid_derived'),2)}}</th>
        <th>{{number_format($results->sum('penalties_repaid_derived'),2)}}</th>
        <th>{{number_format($results->sum('total')-$results->sum('balance'),2)}}</th>
        <th>{{number_format($results->sum('balance'),2)}}</th>

    </tr>
    </tfoot>
</table>
