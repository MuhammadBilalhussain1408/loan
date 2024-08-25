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
<h3 class="text-center">Staff Performance</h3>
<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th colspan="2">Start Date: {{$start_date}}</th>
        <th colspan="2">End Date: {{$end_date}}</th>
        <th colspan="2">
            @if(!empty($results->first()) && !empty($user_id))
                Staff:
                {{$results->first()->name??''}}
            @endif
        </th>
    </tr>
    <tr class="green-heading">
        <th class="p-2 font-medium text-gray-500">Staff</th>
        <th class="p-2 font-medium text-gray-500">Total Clients</th>
        <th class="p-2 font-medium text-gray-500">Total Loans</th>
        <th class="p-2 font-medium text-gray-500">Total Savings</th>
        <th class="p-2 font-medium text-gray-500">Total Loan Principal</th>
        <th class="p-2 font-medium text-gray-500">Total Loan Repayments</th>
    </tr>
    </thead>
    <tbody>

    @foreach($results as $key)
        <tr>
            <td>{{ $key->name }}</td>
            <td>{{ number_format( $key->total_clients) }}</td>
            <td>{{ number_format( $key->total_loans) }}</td>
            <td>{{ number_format( $key->total_savings) }}</td>
            <td>{{ number_format( $key->total_principal,2) }}</td>
            <td>{{ number_format( $key->total_repayments,2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td><b>Total</b></td>
        <td>{{number_format($results->sum('total_clients'))}}</td>
        <td>{{number_format($results->sum('total_loans'))}}</td>
        <td>{{number_format($results->sum('total_savings'))}}</td>
        <td>{{number_format($results->sum('total_principal'),2)}}</td>
        <td>{{number_format($results->sum('total_repayments'),2)}}</td>
    </tr>
    </tfoot>
</table>
