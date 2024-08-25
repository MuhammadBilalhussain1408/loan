<h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
<h3 class="text-center">Savings Balance</h3>
<div style="margin-bottom: 20px">
    <h4>By Branch</h4>
    <table class="table table-bordered table-striped table-hover">
        <thead>
        <tr class="green-heading">
            <th class="p-2 font-medium text-gray-500">Branch</th>
            <th class="p-2 font-medium text-gray-500">Deposits</th>
            <th class="p-2 font-medium text-gray-500">Withdrawals</th>
            <th class="p-2 font-medium text-gray-500">Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalDebits = 0;
        $totalCredits = 0;
        $totalBalance = 0;
        ?>
        @foreach($results['by_branch'] as $key)
                <?php
                $totalDebits += $key['debit'];
                $totalCredits += $key['credit'];
                $totalBalance += $key['balance'];
                ?>
            <tr>
                <td>{{ $key['branch'] }}</td>

                <td>{{ number_format( $key['credit'],2) }}</td>
                <td>{{ number_format( $key['debit'],2) }}</td>
                <td>{{ number_format( $key['balance'],2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td><b>Total</b></td>

            <td>{{number_format($totalCredits,2)}}</td>
            <td>{{number_format($totalDebits,2)}}</td>
            <td>{{number_format($totalBalance,2)}}</td>
        </tr>
        </tfoot>
    </table>
</div>
<div style="margin-bottom: 20px">
    <h4>By Savings Officer</h4>
    <table class="table table-bordered table-striped table-hover">
        <thead>
        <tr class="green-heading">
            <th class="p-2 font-medium text-gray-500">Savings Officer</th>
            <th class="p-2 font-medium text-gray-500">Deposits</th>
            <th class="p-2 font-medium text-gray-500">Withdrawals</th>
            <th class="p-2 font-medium text-gray-500">Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalDebits = 0;
        $totalCredits = 0;
        $totalBalance = 0;
        ?>
        @foreach($results['by_savings_officer'] as $key)
                <?php
                $totalDebits += $key['debit'];
                $totalCredits += $key['credit'];
                $totalBalance += $key['balance'];
                ?>
            <tr>
                <td>{{ $key['savings_officer'] }}</td>

                <td>{{ number_format( $key['credit'],2) }}</td>
                <td>{{ number_format( $key['debit'],2) }}</td>
                <td>{{ number_format( $key['balance'],2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td><b>Total</b></td>

            <td>{{number_format($totalCredits,2)}}</td>
            <td>{{number_format($totalDebits,2)}}</td>
            <td>{{number_format($totalBalance,2)}}</td>
        </tr>
        </tfoot>
    </table>
</div>
<div style="margin-bottom: 20px">
    <h4>By Date</h4>
    <table class="table table-bordered table-striped table-hover">
        <thead>
        <tr class="green-heading">
            <th class="p-2 font-medium text-gray-500">Date</th>
            <th class="p-2 font-medium text-gray-500">Deposits</th>
            <th class="p-2 font-medium text-gray-500">Withdrawals</th>
            <th class="p-2 font-medium text-gray-500">Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalDebits = 0;
        $totalCredits = 0;
        $totalBalance = 0;
        ?>
        @foreach($results['by_date'] as $key)
                <?php
                $totalDebits += $key['debit'];
                $totalCredits += $key['credit'];
                $totalBalance += $key['balance'];
                ?>
            <tr>
                <td>{{ $key['submitted_on'] }}</td>

                <td>{{ number_format( $key['credit'],2) }}</td>
                <td>{{ number_format( $key['debit'],2) }}</td>
                <td>{{ number_format( $key['balance'],2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td><b>Total</b></td>

            <td>{{number_format($totalCredits,2)}}</td>
            <td>{{number_format($totalDebits,2)}}</td>
            <td>{{number_format($totalBalance,2)}}</td>
        </tr>
        </tfoot>
    </table>
</div>


