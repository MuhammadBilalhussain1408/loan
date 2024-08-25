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
<h3 class="text-center">Savings Transactions</h3>
<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th colspan="2">Start Date: {{$start_date}}</th>
        <th colspan="2">End Date: {{$end_date}}</th>
        <th colspan="1">
            @if(!empty($results->first()) && !empty($branch_id))
                Branch:

                {{$results->first()->savings->branch??''}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($savings_product_id))
                Product:

                {{$results->first()->product??''}}
            @endif
        </th>
        <th colspan="1">
            @if(!empty($results->first()) && !empty($savings_officer_id))
                Savings Officer:
                {{$results->first()->savings_officer??''}}
            @endif
        </th>
    </tr>
    <tr class="green-heading">
        <th class="p-2 font-medium text-gray-500">Client</th>
        <th class="p-2 font-medium text-gray-500">Savings Officer</th>
        <th class="p-2 font-medium text-gray-500">Savings#</th>
        <th class="p-2 font-medium text-gray-500">Product</th>
        <th class="p-2 font-medium text-gray-500">Deposits</th>
        <th class="p-2 font-medium text-gray-500">Withdrawals</th>
        <th class="p-2 font-medium text-gray-500">Balance</th>
        <th class="p-2 font-medium text-gray-500">Created Date</th>
    </tr>
    </thead>
    <tbody>

    @foreach($results as $key)
        <tr>
            <td>{{ $key->client }}</td>
            <td>
                {{$key->savings_officer}}
            </td>
            <td>{{ $key->id }}</td>
            <td>{{ $key->product }}</td>
            <td>{{ number_format( $key->credit,2) }}</td>
            <td>{{ number_format( $key->debit,2) }}</td>
            <td>{{ number_format( $key->balance,2) }}</td>
            <td>{{ $key->submitted_on_date }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4"><b>Total</b></td>
        <td>{{number_format($results->sum('credit'),2)}}</td>
        <td>{{number_format($results->sum('debit'),2)}}</td>
        <td>{{number_format($results->sum('balance'),2)}}</td>
        <td></td>
    </tr>
    </tfoot>
</table>
