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
        font-size: 9px;
    }

    .bg-gray {
        background-color: grey;
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

    .header {
        text-align: center;
    }

    .title {
        font-weight: bold;
    }

    .title-content {
        margin-left: 10px;
    }

    .clearfix {
        clear: both;
    }

    * {
        overflow: auto
    }
</style>
<div class="header">
    <h3 class="text-center">{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</h3>
    <h3 class="text-center">Ledger From {{$start_date}}
        to {{$end_date}}</h3>
</div>
<div class="clearfix">
    <table class="table">
        <thead class="green-heading">
        <tr>
            <th colspan="3">
                Branch: {{$branch_id?(\App\Models\Branch::find($branch_id)->name??''):'All'}}
            </th>
            <th colspan="2">
                Currency: {{$currency_id?(\App\Models\Currency::find($currency_id)->name??''):'All'}}
            </th>
        </tr>
        <tr>
            <th colspan="1">
                Account Type
            </th>
            <th colspan="1">
                Account Name
            </th>
            <th colspan="1">
                Code
            </th>
            <th colspan="1">
                Debit
            </th>
            <th colspan="1">
                Credit
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($results as $result)
            <tr
                class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t px-6 py-4 ">
                    @if($result->account_type==='fixed_asset')
                        <span> Fixed Asset</span>
                    @endif
                    @if($result->account_type==='current_asset')
                        <span> Current Asset</span>
                    @endif
                    @if($result->account_type==='other_current_asset')
                        <span> Other Current Asset</span>
                    @endif
                    @if($result->account_type==='other_asset')
                        <span> Other Asset</span>
                    @endif
                    @if($result->account_type==='cash')
                        <span>Cash</span>
                    @endif
                    @if($result->account_type==='bank')
                        <span>Bank</span>
                    @endif
                    @if($result->account_type==='stock')
                        <span>Stock</span>
                    @endif
                    @if($result->account_type==='other_current_liability')
                        <span>Other Current Liability</span>
                    @endif
                    @if($result->account_type==='credit_card')
                        <span>Credit Card</span>
                    @endif
                    @if($result->account_type==='long_term_liability')
                        <span>Long Term Liability</span>
                    @endif
                    @if($result->account_type==='other_liability')
                        <span>Other Liability</span>
                    @endif
                    @if($result->account_type==='income_tax')
                        <span>Income Tax</span>
                    @endif
                    @if($result->account_type==='income')
                        <span>Income</span>
                    @endif
                    @if($result->account_type==='other_income')
                        <span>Other Income</span>
                    @endif
                    @if($result->account_type==='expense')
                        <span>Expense</span>
                    @endif
                    @if($result->account_type==='cost_of_goods_sold')
                        <span>Cost of Goods Sold</span>
                    @endif
                    @if($result->account_type==='other_expense')
                        <span>Other Expense</span>
                    @endif
                    @if($result->account_type==='equity')
                        <span>Equity</span>
                    @endif
                    @if($result->account_type==='accounts_receivable')
                        <span>Accounts Receivable</span>
                    @endif
                    @if($result->account_type==='accounts_payable')
                        <span>Accounts Payable</span>
                    @endif
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{$result->name}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{$result->gl_code}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->debit,2)}}</span>
                </td>
                <td class="border-t px-6 py-4 ">
                    <span>{{number_format($result->credit,2)}}</span>
                </td>
            </tr>
        @endforeach
        <tr class="text-left">
            <td class="border-t px-6 py-4 font-bold text-2xl" colspan="3">
                <strong>Total</strong>
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong><span>{{number_format($results->sum('debit'),2)}}</span></strong>
            </td>
            <td class="border-t px-6 py-4 font-bold text-2xl">
                <strong><span>{{number_format($results->sum('credit'),2)}}</span></strong>
            </td>
        </tr>
        </tbody>
    </table>
</div>
