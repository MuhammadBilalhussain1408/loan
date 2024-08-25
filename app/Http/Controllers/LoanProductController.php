<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Currency;
use App\Models\Fund;
use App\Models\LoanApplicationChecklist;
use App\Models\LoanCharge;
use App\Models\LoanCreditCheck;
use App\Models\LoanProduct;
use App\Models\LoanProductLinkedCharge;
use App\Models\LoanProductLinkedCreditCheck;
use App\Models\LoanTransactionProcessingStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.products.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.products.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.products.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.products.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';
        $search = $request->s;
        $results = LoanProduct::filter(\request()->only('search', 'currency_id'))
            ->with(['currency', 'disbursementChannel', 'processingStrategy'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('LoanProducts/Index', [
            'filters' => \request()->all('search'),
            'results' => $results
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        $chartOfAccounts = ChartOfAccount::where('active', 1)->get();
        return Inertia::render('LoanProducts/Create', [
            'currencies' => Currency::where('active', 1)->orderBy('name')->get(),
            'checklists' => LoanApplicationChecklist::get(),
            'assets' => $chartOfAccounts->where('account_type', 'asset'),
            'expenses' => $chartOfAccounts->where('account_type', 'expense'),
            'income' => $chartOfAccounts->where('account_type', 'income'),
            'liabilities' => $chartOfAccounts->where('account_type', 'liability'),
            'charges' => LoanCharge::where('active', 1)->get(),
            'creditChecks' => LoanCreditCheck::where('active', 1)->get(),
            'transactionProcessingStrategies' => LoanTransactionProcessingStrategy::get(),
        ]);
    }

    public function get_charges($id)
    {

        $charges = LoanCharge::where('active', 1)->where('currency_id', $id)->get();
        return response()->json(["collection" => $charges]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency_id' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'minimum_principal' => ['required', 'numeric', 'lte:maximum_principal'],
            'default_principal' => ['required', 'numeric', 'lte:maximum_principal'],
            'maximum_principal' => ['required', 'numeric', 'gte:minimum_principal'],
            'minimum_loan_term' => ['required', 'numeric', 'lte:maximum_loan_term'],
            'default_loan_term' => ['required', 'numeric', 'lte:maximum_loan_term'],
            'maximum_loan_term' => ['required', 'numeric', 'gte:minimum_loan_term'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'minimum_interest_rate' => ['required', 'numeric', 'lte:maximum_interest_rate'],
            'default_interest_rate' => ['required', 'numeric', 'lte:maximum_interest_rate'],
            'maximum_interest_rate' => ['required', 'numeric', 'gte:minimum_interest_rate'],
            'interest_rate_type' => ['required'],
            'grace_on_principal_paid' => ['required'],
            'grace_on_interest_paid' => ['required'],
            'grace_on_interest_charged' => ['required'],
            'interest_methodology' => ['required'],
            'amortization_method' => ['required'],
            'auto_disburse' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'fund_source_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'loan_portfolio_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'penalties_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'fees_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'overpayments_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_recovery_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'losses_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'suspended_income_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ], [
            'minimum_principal.lte' => 'Minimum Principal cannot be greater than maximum principal',
            'default_principal.lte' => 'Default Principal cannot be greater than maximum principal',
            'maximum_principal.gte' => 'Maximum Principal cannot be smaller than minimum principal',
            'minimum_loan_term.lte' => 'Minimum loan term cannot be greater than maximum loan term',
            'default_loan_term.lte' => 'Default loan term cannot be greater than maximum loan term',
            'maximum_loan_term.gte' => 'Maximum loan term cannot be smaller than maximum loan term',
            'minimum_interest_rate.lte' => 'Minimum interest cannot be greater than maximum interest rate',
            'default_interest_rate.lte' => 'Default interest cannot be greater than maximum interest rate',
            'maximum_interest_rate.gte' => 'Maximum interest cannot be smaller than minimum interest rate',
            'minimum_principal.numeric' => 'Minimum Principal must be a number',
            'default_principal.numeric' => 'Default Principal must be a number',
            'maximum_principal.numeric' => 'Maximum Principal must be a number',
            'minimum_loan_term.numeric' => 'Minimum loan term must be a number',
            'default_loan_term.numeric' => 'Default loan term must be a number',
            'maximum_loan_term.numeric' => 'Maximum loan term must be a number',
            'minimum_interest_rate.numeric' => 'Minimum interest must be a number',
            'default_interest_rate.numeric' => 'Default interest must be a number',
            'maximum_interest_rate.numeric' => 'Maximum interest must be a number',
            'repayment_frequency.numeric' => 'Repayment frequency must be a number',
        ]);
        $product = new LoanProduct();
        $product->currency_id = $request->currency_id;
        $product->loan_transaction_processing_strategy_id = $request->loan_transaction_processing_strategy_id;
        $product->loan_application_checklist_id = $request->loan_application_checklist_id;
        $product->created_by_id = Auth::id();
        $product->fund_source_chart_of_account_id = $request->fund_source_chart_of_account_id;
        $product->loan_portfolio_chart_of_account_id = $request->loan_portfolio_chart_of_account_id;
        $product->interest_receivable_chart_of_account_id = $request->interest_receivable_chart_of_account_id;
        $product->penalties_receivable_chart_of_account_id = $request->penalties_receivable_chart_of_account_id;
        $product->fees_receivable_chart_of_account_id = $request->fees_receivable_chart_of_account_id;
        $product->fees_chart_of_account_id = $request->fees_chart_of_account_id;
        $product->overpayments_chart_of_account_id = $request->overpayments_chart_of_account_id;
        $product->income_from_interest_chart_of_account_id = $request->income_from_interest_chart_of_account_id;
        $product->income_from_penalties_chart_of_account_id = $request->income_from_penalties_chart_of_account_id;
        $product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
        $product->income_from_recovery_chart_of_account_id = $request->income_from_recovery_chart_of_account_id;
        $product->losses_written_off_chart_of_account_id = $request->losses_written_off_chart_of_account_id;
        $product->interest_written_off_chart_of_account_id = $request->interest_written_off_chart_of_account_id;
        $product->suspended_income_chart_of_account_id = $request->suspended_income_chart_of_account_id;
        $product->name = $request->name;
        $product->short_name = $request->short_name;
        $product->description = $request->description;
        $product->decimals = $request->decimals;
        $product->minimum_principal = $request->minimum_principal;
        $product->default_principal = $request->default_principal;
        $product->maximum_principal = $request->maximum_principal;
        $product->minimum_loan_term = $request->minimum_loan_term;
        $product->default_loan_term = $request->default_loan_term;
        $product->maximum_loan_term = $request->maximum_loan_term;
        $product->repayment_frequency = $request->repayment_frequency;
        $product->repayment_frequency_type = $request->repayment_frequency_type;
        $product->minimum_interest_rate = $request->minimum_interest_rate;
        $product->default_interest_rate = $request->default_interest_rate;
        $product->maximum_interest_rate = $request->maximum_interest_rate;
        $product->interest_rate_type = $request->interest_rate_type;
        $product->grace_on_principal_paid = $request->grace_on_principal_paid;
        $product->grace_on_interest_paid = $request->grace_on_interest_paid;
        $product->grace_on_interest_charged = $request->grace_on_interest_charged;
        $product->interest_methodology = $request->interest_methodology;
        $product->amortization_method = $request->amortization_method;
        $product->accounting_rule = $request->accounting_rule;
        $product->auto_disburse = $request->auto_disburse;
        $product->deduct_interest_from_principal = $request->deduct_interest_from_principal ? 1 : 0;
        $product->disallow_interest_rate_adjustment = $request->disallow_interest_rate_adjustment ? 1 : 0;
        $product->exclude_holidays = $request->exclude_holidays;
        $product->exclude_weekends = $request->exclude_weekends;
        $product->active = $request->active;
        $product->save();
        //save charges
        if (!empty($request->selected_charges)) {
            foreach ($request->selected_charges as $key) {
                $product_charge = new LoanProductLinkedCharge();
                $product_charge->loan_product_id = $product->id;
                $product_charge->loan_charge_id = $key;
                $product_charge->save();
            }
        }
        //save credit checks
        if (!empty($request->selected_credit_checks)) {

            foreach ($request->selected_credit_checks as $key) {
                if (!empty($key)) {
                    $product_credit_check = new LoanProductLinkedCreditCheck();
                    $product_credit_check->loan_product_id = $product->id;
                    $product_credit_check->loan_credit_check_id = $key;
                    $product_credit_check->save();
                }
            }
        }
        return redirect()->route('loans.products.index')->with('success', 'Successfully created');
    }

    /**
     * Show the form for editing the specified resource.
     * @param LoanProduct $product
     * @return \Inertia\Response
     */
    public function edit(LoanProduct $product)
    {

        $product->selected_charges = $product->charges->pluck('loan_charge_id')->toArray();
        $chartOfAccounts = ChartOfAccount::where('active', 1)->get();
        return Inertia::render('LoanProducts/Edit', [
            'product' => $product,
            'currencies' => Currency::where('active', 1)->orderBy('name')->get(),
            'checklists' => LoanApplicationChecklist::get(),
            'assets' => $chartOfAccounts->where('account_type', 'asset'),
            'expenses' => $chartOfAccounts->where('account_type', 'expense'),
            'income' => $chartOfAccounts->where('account_type', 'income'),
            'liabilities' => $chartOfAccounts->where('account_type', 'liability'),
            'charges' => LoanCharge::where('active', 1)->get(),
            'creditChecks' => LoanCreditCheck::where('active', 1)->get(),
            'transactionProcessingStrategies' => LoanTransactionProcessingStrategy::get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanProduct $product
     * @return RedirectResponse
     */
    public function update(Request $request, LoanProduct $product)
    {
        $request->validate([
            'currency_id' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'minimum_principal' => ['required', 'numeric', 'lte:maximum_principal'],
            'default_principal' => ['required', 'numeric', 'lte:maximum_principal'],
            'maximum_principal' => ['required', 'numeric', 'gte:minimum_principal'],
            'minimum_loan_term' => ['required', 'numeric', 'lte:maximum_loan_term'],
            'default_loan_term' => ['required', 'numeric', 'lte:maximum_loan_term'],
            'maximum_loan_term' => ['required', 'numeric', 'gte:minimum_loan_term'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'minimum_interest_rate' => ['required', 'numeric', 'lte:maximum_interest_rate'],
            'default_interest_rate' => ['required', 'numeric', 'lte:maximum_interest_rate'],
            'maximum_interest_rate' => ['required', 'numeric', 'gte:minimum_interest_rate'],
            'interest_rate_type' => ['required'],
            'grace_on_principal_paid' => ['required'],
            'grace_on_interest_paid' => ['required'],
            'grace_on_interest_charged' => ['required'],
            'interest_methodology' => ['required'],
            'amortization_method' => ['required'],
            'auto_disburse' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'fund_source_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'loan_portfolio_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'penalties_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'fees_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'overpayments_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_recovery_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'losses_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'suspended_income_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ], [
            'minimum_principal.lte' => 'Minimum Principal cannot be greater than maximum principal',
            'default_principal.lte' => 'Default Principal cannot be greater than maximum principal',
            'maximum_principal.gte' => 'Maximum Principal cannot be smaller than minimum principal',
            'minimum_loan_term.lte' => 'Minimum loan term cannot be greater than maximum loan term',
            'default_loan_term.lte' => 'Default loan term cannot be greater than maximum loan term',
            'maximum_loan_term.gte' => 'Maximum loan term cannot be smaller than maximum loan term',
            'minimum_interest_rate.lte' => 'Minimum interest cannot be greater than maximum interest rate',
            'default_interest_rate.lte' => 'Default interest cannot be greater than maximum interest rate',
            'maximum_interest_rate.gte' => 'Maximum interest cannot be smaller than minimum interest rate',
            'minimum_principal.numeric' => 'Minimum Principal must be a number',
            'default_principal.numeric' => 'Default Principal must be a number',
            'maximum_principal.numeric' => 'Maximum Principal must be a number',
            'minimum_loan_term.numeric' => 'Minimum loan term must be a number',
            'default_loan_term.numeric' => 'Default loan term must be a number',
            'maximum_loan_term.numeric' => 'Maximum loan term must be a number',
            'minimum_interest_rate.numeric' => 'Minimum interest must be a number',
            'default_interest_rate.numeric' => 'Default interest must be a number',
            'maximum_interest_rate.numeric' => 'Maximum interest must be a number',
            'repayment_frequency.numeric' => 'Repayment frequency must be a number',
        ]);
        $product->currency_id = $request->currency_id;
        $product->loan_transaction_processing_strategy_id = $request->loan_transaction_processing_strategy_id;
        $product->loan_application_checklist_id = $request->loan_application_checklist_id;
        $product->fund_source_chart_of_account_id = $request->fund_source_chart_of_account_id;
        $product->loan_portfolio_chart_of_account_id = $request->loan_portfolio_chart_of_account_id;
        $product->interest_receivable_chart_of_account_id = $request->interest_receivable_chart_of_account_id;
        $product->penalties_receivable_chart_of_account_id = $request->penalties_receivable_chart_of_account_id;
        $product->fees_receivable_chart_of_account_id = $request->fees_receivable_chart_of_account_id;
        $product->fees_chart_of_account_id = $request->fees_chart_of_account_id;
        $product->overpayments_chart_of_account_id = $request->overpayments_chart_of_account_id;
        $product->income_from_interest_chart_of_account_id = $request->income_from_interest_chart_of_account_id;
        $product->income_from_penalties_chart_of_account_id = $request->income_from_penalties_chart_of_account_id;
        $product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
        $product->income_from_recovery_chart_of_account_id = $request->income_from_recovery_chart_of_account_id;
        $product->losses_written_off_chart_of_account_id = $request->losses_written_off_chart_of_account_id;
        $product->interest_written_off_chart_of_account_id = $request->interest_written_off_chart_of_account_id;
        $product->suspended_income_chart_of_account_id = $request->suspended_income_chart_of_account_id;
        $product->name = $request->name;
        $product->short_name = $request->short_name;
        $product->description = $request->description;
        $product->decimals = $request->decimals;
        $product->minimum_principal = $request->minimum_principal;
        $product->default_principal = $request->default_principal;
        $product->maximum_principal = $request->maximum_principal;
        $product->minimum_loan_term = $request->minimum_loan_term;
        $product->default_loan_term = $request->default_loan_term;
        $product->maximum_loan_term = $request->maximum_loan_term;
        $product->repayment_frequency = $request->repayment_frequency;
        $product->repayment_frequency_type = $request->repayment_frequency_type;
        $product->minimum_interest_rate = $request->minimum_interest_rate;
        $product->default_interest_rate = $request->default_interest_rate;
        $product->maximum_interest_rate = $request->maximum_interest_rate;
        $product->interest_rate_type = $request->interest_rate_type;
        $product->grace_on_principal_paid = $request->grace_on_principal_paid;
        $product->grace_on_interest_paid = $request->grace_on_interest_paid;
        $product->grace_on_interest_charged = $request->grace_on_interest_charged;
        $product->interest_methodology = $request->interest_methodology;
        $product->amortization_method = $request->amortization_method;
        $product->accounting_rule = $request->accounting_rule;
        $product->auto_disburse = $request->auto_disburse;
        $product->deduct_interest_from_principal = $request->deduct_interest_from_principal ? 1 : 0;
        $product->disallow_interest_rate_adjustment = $request->disallow_interest_rate_adjustment ? 1 : 0;
        $product->exclude_holidays = $request->exclude_holidays;
        $product->exclude_weekends = $request->exclude_weekends;
        $product->active = $request->active;
        $product->save();
        //save charges
        $product->charges()->delete();
        if (!empty($request->selected_charges)) {
            foreach ($request->selected_charges as $key) {
                $product_charge = new LoanProductLinkedCharge();
                $product_charge->loan_product_id = $product->id;
                $product_charge->loan_charge_id = $key;
                $product_charge->save();
            }
        }
        //save credit checks
        $product->creditChecks()->delete();
        if (!empty($request->selected_credit_checks)) {
            foreach ($request->selected_credit_checks as $key) {
                if (!empty($key)) {
                    $product_credit_check = new LoanProductLinkedCreditCheck();
                    $product_credit_check->loan_product_id = $product->id;
                    $product_credit_check->loan_credit_check_id = $key;
                    $product_credit_check->save();
                }
            }
        }

        return redirect()->route('loans.products.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanProduct $product
     * @return RedirectResponse
     */
    public function destroy(LoanProduct $product)
    {
        $product->charges()->delete();
        $product->creditChecks()->delete();
        $product->delete();
        return redirect()->route('loans.products.index')->with('success', 'Successfully deleted');
    }

    public function search(Request $request)
    {
        $search = $request->s;
        $id = $request->id;
        $data = LoanProduct::with(['fund', 'charges', 'charges.charge',  'charges.charge.type',  'charges.charge.option', 'processingStrategy'])->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
            $query->orWhere('short_name', 'like', "%$search%");
            $query->orWhere('description', 'like', "%$search%");
            $query->orWhere('id', 'like', "%$search%");
        })->when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        })->get();
        return response()->json($data);
    }
}
