<?php

namespace App\Http\Controllers;


use App\Events\LoanCreated;
use App\Events\LoanStatusChanged;
use App\Events\TransactionUpdated;
use App\Models\Member;
use App\Models\Currency;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Fund;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanHistory;
use App\Models\LoanLinkedCharge;
use App\Models\LoanOfficerHistory;
use App\Models\LoanProduct;
use App\Models\LoanPurpose;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.index'])->only(['index', 'get_loans', 'show', 'show_application', 'get_applications']);
        $this->middleware(['permission:loans.create'])->only(['create', 'create_member_loan', 'store_member_loan', 'store']);
        $this->middleware(['permission:loans.update'])->only(['edit', 'edit_member_loan', 'update', 'update_member_loan', 'change_loan_officer']);
        $this->middleware(['permission:loans.destroy'])->only(['destroy']);
        $this->middleware(['permission:loans.approve_loan'])->only(['approve_loan', 'undo_approval', 'reject_loan', 'undo_rejection', 'approve_application', 'store_approve_application']);
        $this->middleware(['permission:loans.disburse_loan'])->only(['disburse_loan', 'undo_disbursement']);
        $this->middleware(['permission:loans.withdraw_loan'])->only(['withdraw_loan', 'undo_withdrawn']);
        $this->middleware(['permission:loans.write_off_loan'])->only(['write_off_loan', 'undo_write_off']);
        $this->middleware(['permission:loans.reschedule_loan'])->only(['reschedule_loan']);
        $this->middleware(['permission:loans.close_loan'])->only(['close_loan', 'undo_close']);
        $this->middleware(['permission:loans.calculator'])->only(['calculator']);
        $this->middleware(['permission:loans.transactions.create'])->only(['create_repayment', 'store_repayment', 'create_loan_linked_charge', 'store_loan_linked_charge']);
        $this->middleware(['permission:loans.transactions.edit'])->only(['edit_repayment', 'reverse_repayment', 'update_repayment', 'waive_interest', 'waive_charge']);

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

        $results = Loan::filter(\request()->only('search', 'status', 'member_id', 'fund_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'))
            ->with(['member', 'branch', 'loanOfficer', 'product', 'currency'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        $products = LoanProduct::where('active', 1)->get();
        $currencies = Currency::where('active', 1)->get();
        $purposes = LoanPurpose::get();
        return Inertia::render('Loans/Index', [
            'filters' => \request()->all('search', 'status', 'member_id', 'fund_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'),
            'results' => $results,
            'products' => $products,
            'purposes' => $purposes,
            'currencies' => $currencies,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        $products = LoanProduct::with(['charges', 'charges.charge', 'charges.charge.option', 'charges.charge.type'])->where('active', 1)->get();
        $funds = Fund::all();
        $purposes = LoanPurpose::get();
        $customFields = CustomField::where('category', 'loan')->where('active', 1)->get()->transform(function ($item) {
            $item->field_value = '';
            return $item;
        });
        return Inertia::render('Loans/Create', [
            'member_id' => request('member_id'),
            'products' => $products,
            'funds' => $funds,
            'purposes' => $purposes,
            'customFields' => $customFields
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $product = LoanProduct::find($request->loan_product_id);
        $request->validate([
            'fund_id' => ['required'],
            'loan_product_id' => ['required'],
            'member_id' => ['required'],
            'applied_amount' => ['required', 'numeric', 'lte:' . $product->maximum_principal, 'gte:' . $product->minimum_principal],
            'loan_term' => ['required', 'numeric', 'lte:' . $product->maximum_loan_term, 'gte:' . $product->minimum_loan_term],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);
        $member = Member::find($request->member_id);
        $loan = new Loan();
        $loan->currency_id = $product->currency_id;
        $loan->loan_product_id = $product->id;
        $loan->member_id = $member->id;
        $loan->branch_id = $member->branch_id;
        $loan->loan_transaction_processing_strategy_id = $product->loan_transaction_processing_strategy_id;
        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = $request->expected_first_payment_date;
        $loan->fund_id = $request->fund_id;
        $loan->created_by_id = Auth::id();
        $loan->applied_amount = $request->applied_amount;
        $loan->principal = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        $loan->interest_rate = $product->disallow_interest_rate_adjustment ? $product->default_interest_rate : $request->interest_rate;
        $loan->interest_rate_type = $product->interest_rate_type;
        $loan->grace_on_principal_paid = $product->grace_on_principal_paid;
        $loan->grace_on_interest_paid = $product->grace_on_interest_paid;
        $loan->grace_on_interest_charged = $product->grace_on_interest_charged;
        $loan->interest_methodology = $product->interest_methodology;
        $loan->amortization_method = $product->amortization_method;
        $loan->auto_disburse = $product->auto_disburse;
        $loan->deduct_interest_from_principal = $product->deduct_interest_from_principal;
        $loan->submitted_on_date = date("Y-m-d");
        $loan->submitted_by_user_id = Auth::id();
        $loan->save();
        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $loan->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $loan->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        //save charges
        if (!empty($request->selected_charges)) {
            foreach ($request->selected_charges as $key) {
                $charge = LoanCharge::find($key['loan_charge_id']);
                $linkedCharge = new LoanLinkedCharge();
                $linkedCharge->loan_id = $loan->id;
                $linkedCharge->name = $charge->name;
                $linkedCharge->loan_charge_id = $charge->id;
                if ($charge->allow_override == 1) {
                    $linkedCharge->amount = $key['amount'];
                } else {
                    $linkedCharge->amount = $charge->amount;
                }
                $linkedCharge->loan_charge_type_id = $charge->loan_charge_type_id;
                $linkedCharge->loan_charge_option_id = $charge->loan_charge_option_id;
                $linkedCharge->is_penalty = $charge->is_penalty;
                $linkedCharge->save();
            }
        }
        $loanHistory = new LoanHistory();
        $loanHistory->loan_id = $loan->id;
        $loanHistory->created_by_id = Auth::id();
        $loanHistory->user = Auth::user()->name;
        $loanHistory->action = 'Loan Created';
        $loanHistory->save();
        if ($loan->loan_officer_id) {
            $loanOfficerHistory = new LoanOfficerHistory();
            $loanOfficerHistory->loan_id = $loan->id;
            $loanOfficerHistory->created_by_id = Auth::id();
            $loanOfficerHistory->loan_officer_id = $request->loan_officer_id;
            $loanOfficerHistory->start_date = date("Y-m-d");
            $loanOfficerHistory->save();
        }
        //fire loan created event
        event(new LoanCreated($loan));
        return redirect()->route('loans.show', $loan->id)->with('success', 'Successfully created');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function rescheduleLoan(Request $request, Loan $loan)
    {
        $request->validate([
            'rescheduled_from_date' => ['required', 'date'],
            'rescheduled_on_date' => ['required', 'date'],
            'rescheduled_first_payment_date' => ['required_if:reschedule_first_payment_date,on', 'date'],
            'reschedule_grace_on_principal_paid' => ['nullable', 'numeric'],
            'reschedule_grace_on_interest_paid' => ['nullable', 'numeric'],
            'reschedule_extra_installments' => ['required_if:reschedule_add_extra_installments,on', 'numeric'],
            'reschedule_interest_rate' => ['required_if:reschedule_adjust_loan_interest_rate,on', 'numeric'],
        ]);


        if (empty($loan->schedules->where('due_date', $request->rescheduled_from_date)->first())) {
            return redirect()->back()->with('error', 'No installment schedules found for the selected from date');
        }
        $reschedule_principal = $loan->schedules->sum('principal') - $loan->schedules->where('due_date', '<', $request->rescheduled_from_date)->sum('principal');
        LoanRepaymentSchedule::where('due_date', '>=', $request->rescheduled_from_date)->where('loan_id', $loan->id)->delete();
        $interest_rate = determine_period_interest_rate($request->reschedule_interest_rate ?: $loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type);
        $balance = round($reschedule_principal, $loan->decimals);
        $period = $loan->schedules->where('due_date', '>=', $request->rescheduled_from_date)->count() + $request->reschedule_extra_installments;
        $payment_from_date = $request->rescheduled_on_date;
        $next_payment_date = $request->rescheduled_first_payment_date ?: $loan->schedules->where('due_date', '>=', $request->rescheduled_from_date)->first()->due_date;

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = Auth::id();
            $loan_repayment_schedule->loan_id = $loan->id;
            $loan_repayment_schedule->installment = $i;
            $loan_repayment_schedule->due_date = $next_payment_date;
            $loan_repayment_schedule->from_date = $payment_from_date;
            $date = explode('-', $next_payment_date);
            $loan_repayment_schedule->month = $date[1];
            $loan_repayment_schedule->year = $date[0];
            //determine which method to use
            //flat  method
            if ($loan->interest_methodology == 'flat') {
                $principal = round($reschedule_principal / $period, $loan->decimals);
                $interest = round($interest_rate * $principal, $loan->decimals) / $period;
                if ($request->reschedule_grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan->interest_methodology == 'declining_balance') {
                if ($loan->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $reschedule_principal, $period), $loan->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan->decimals);
                    $principal = round(($amortized_payment - $interest), $loan->decimals);
                    if ($request->reschedule_grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan->amortization_method == 'equal_principal_payments') {
                    $principal = round($reschedule_principal / $period, $loan->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan->decimals);
                    if ($request->reschedule_grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }

            }
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
            $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->save();
        }

        $loan->load('schedules');
        $total_principal = $loan->schedules->sum('principal');
        $total_interest = $loan->schedules->sum('interest');
        foreach ($loan->charges->whereIn('loan_charge_type_id', LoanTransactionType::whereIn('name', ['Specified Due Date', 'Installment Fees'])->get()->pluck('id')->toArray()) as $key) {
            //installment_fee
            $total_calculated_amount = 0;
            if ($key->charge->type->name == 'Installment Fees') {
                if ($key->charge->option->name === 'Flat') {
                    $key->calculated_amount = $key->amount;
                }
                if ($key->charge->option->name === 'Principal due on installment') {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                }
                if ($key->charge->option->name === 'Principal + Interest due on installment') {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), $loan->decimals);
                }
                if ($key->charge->option->name === 'Interest due on installment') {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), $loan->decimals);
                }
                if ($key->charge->option->name === 'Total Outstanding Loan Principal') {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                }
                if ($key->charge->option->name === 'Percentage of Original Loan Principal per Installment') {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                }
                if ($key->charge->option->name === 'Original Loan Principal') {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), $loan->decimals);
                }

                //reverse and create new transaction
                if (!empty($key->transaction)) {
                    $key->transaction->credit = $key->transaction->amount;
                    $key->transaction->debit = $key->transaction->amount;
                    $key->transaction->reversed = 1;
                    $key->transaction->save();
                }

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->branch_id = $loan->branch_id;
                $loan_transaction->name = 'Fee Applied';
                $loan_transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Apply Charges')->first()->id;
                $loan_transaction->submitted_on = $loan->disbursed_on_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $key->calculated_amount;
                $loan_transaction->debit = $key->calculated_amount;
                $loan_transaction->reversible = 1;
                $loan_transaction->save();
                $key->loan_transaction_id = $loan_transaction->id;
                $key->save();
                foreach ($loan->schedules->where('due_date', '>=', $request->rescheduled_from_date) as $loan_repayment_schedule) {
                    if ($key->charge->option->name === 'Principal due on installment') {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), $loan->decimals);
                    } elseif ($key->charge->option->name === 'Principal + Interest due on installment') {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), $loan->decimals);
                    } elseif ($key->charge->option->name === 'Interest due on installment') {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->interest / 100), $loan->decimals);
                    } else {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    }
                    $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest + $loan_repayment_schedule->fees;
                    $loan_repayment_schedule->save();
                }
            }
        }

        $loan->save();

        $loan->expected_maturity_date = $next_payment_date;
        $loan->rescheduled_on_date = $request->rescheduled_on_date;
        $loan->rescheduled_notes = $request->rescheduled_notes;
        $loan->rescheduled_by_user_id = Auth::id();
        $loan->save();
        $loanHistory = new LoanHistory();
        $loanHistory->loan_id = $loan->id;
        $loanHistory->created_by_id = Auth::id();
        $loanHistory->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loanHistory->action = 'Loan Rescheduled';
        $loanHistory->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Reschedule Loan');
        return redirect()->route('loans.show', $loan->id)->with('success', 'Successfully saved');
    }


    public function show(Loan $loan)
    {
        $loan->load(['member', 'branch', 'loanOfficer', 'product', 'currency', 'fund', 'purpose', 'transactionProcessingStrategy']);
        if (empty($loan->product)) {
            return redirect()->back()->with('error', 'Linked product not found');
        }
        if (empty($loan->member)) {
            return redirect()->back()->with('error', 'Linked member not found');
        }
        $customFields = CustomField::where('category', 'loan')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($loan) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'loan')->where('parent_id', $loan->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $loan->custom_fields = $customFields;
        $balance = $loan->principal;
        //arrears
        $arrearsDays = 0;
        $arrearsAmount = 0;
        $timelyRepayments = 0;
        $principalOverdue = 0;
        $interestOverdue = 0;
        $feesOverdue = 0;
        $penaltiesOverdue = 0;
        $totalDueRepayments = $loan->schedules->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->count();
        $arrearsLastSchedule = $loan->schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
        if (!empty($arrearsLastSchedule)) {
            $overdueSchedules = $loan->schedules->where('due_date', '<=', $arrearsLastSchedule->due_date);
            $principalOverdue = $overdueSchedules->sum('principal') - $overdueSchedules->sum('principal_written_off_derived') - $overdueSchedules->sum('principal_repaid_derived');
            $interestOverdue = $overdueSchedules->sum('interest') - $overdueSchedules->sum('interest_written_off_derived') - $overdueSchedules->sum('interest_repaid_derived') - $overdueSchedules->sum('interest_waived_derived');
            $feesOverdue = $overdueSchedules->sum('fees') - $overdueSchedules->sum('fees_written_off_derived') - $overdueSchedules->sum('fees_repaid_derived') - $overdueSchedules->sum('fees_waived_derived');
            $penaltiesOverdue = $overdueSchedules->sum('penalties') - $overdueSchedules->sum('penalties_written_off_derived') - $overdueSchedules->sum('penalties_repaid_derived') - $overdueSchedules->sum('penalties_waived_derived');
            $arrearsDays = $arrearsDays + Carbon::today()->diffInDays(Carbon::parse($overdueSchedules->sortBy('due_date')->first()->due_date));
        }
        $loan->schedules->transform(function ($item) use (&$balance, &$arrearsDays, &$arrearsAmount, &$timelyRepayments) {
            $item->total = $item->principal - $item->principal_written_off_derived + $item->interest - $item->interest_written_off_derived - $item->interest_waived_derived + $item->fees - $item->fees_written_off_derived - $item->fees_waived_derived + $item->penalties - $item->penalties_written_off_derived - $item->penalties_waived_derived;
            $item->total_paid = $item->principal_repaid_derived + $item->interest_repaid_derived + $item->fees_repaid_derived + $item->penalties_repaid_derived;
            if ($item->total_due <= 0) {
                if (Carbon::parse($item->paid_by_date)->greaterThan(Carbon::parse($item->due_date))) {
                    $item->late_payment = true;
                    $arrearsAmount += $item->total_due;
                } else {
                    $timelyRepayments++;
                    $item->late_payment = false;
                }
            } else {
                if (Carbon::today()->greaterThan(Carbon::parse($item->due_date))) {
                    $item->late_payment = true;
                    $arrearsAmount += $item->total_due;
                } else {
                    $item->late_payment = false;
                }
            }
            $balance = $balance - $item->principal - $item->principal_written_off_derived;
            $item->balance = $balance;
            $item->days = Carbon::parse($item->due_date)->diffInDays(Carbon::parse($item->from_date));
            return $item;
        });
        if ($totalDueRepayments > 0) {
            $timelyRepayments = round($timelyRepayments * 100 / $totalDueRepayments);
        }
        $loan->timely_repayments = $timelyRepayments;
        $loan->arrears_days = $arrearsDays;
        $loan->arrears_amount = $arrearsAmount;
        $loan->principal_overdue = $principalOverdue;
        $loan->interest_overdue = $interestOverdue;
        $loan->fees_overdue = $feesOverdue;
        $loan->penalties_overdue = $penaltiesOverdue;
        return Inertia::render('Loans/Show', [
            'loan' => $loan,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }


    public function edit(Loan $loan)
    {
        $loan->load(['member', 'product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);
        $funds = Fund::all();
        $purposes = LoanPurpose::get();
        $customFields = CustomField::where('category', 'loan')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($loan) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'loan')->where('parent_id', $loan->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $loan->custom_fields = $customFields;
        $loan->selected_charges = $loan->charges->map(function ($item) {
            return [
                'id' => $item->id,
                'loan_charge_id' => $item->loan_charge_id,
                'name' => $item->charge->name,
                'type' => $item->charge->type->name,
                'option' => $item->charge->option->name,
                'amount' => $item->amount,
            ];
        })->toArray();
        return Inertia::render('Loans/Edit', [
            'loan' => $loan,
            'funds' => $funds,
            'purposes' => $purposes,
            'customFields' => $customFields
        ]);
    }

    public function update(Request $request, Loan $loan)
    {

        $request->validate([
            'fund_id' => ['required'],
            'applied_amount' => ['required', 'numeric', 'lte:' . $loan->product->maximum_principal, 'gte:' . $loan->product->minimum_principal],
            'loan_term' => ['required', 'numeric', 'lte:' . $loan->product->maximum_loan_term, 'gte:' . $loan->product->minimum_loan_term],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);

        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = $request->expected_first_payment_date;
        $loan->fund_id = $request->fund_id;
        $loan->applied_amount = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        if (!$loan->product->disallow_interest_rate_adjustment) {
            $loan->interest_rate = $request->interest_rate;
        }
        $loan->save();
        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $loan->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $loan->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        //save charges
        $loan->charges()->delete();
        if (!empty($request->selected_charges)) {
            foreach ($request->selected_charges as $key) {
                $charge = LoanCharge::find($key['loan_charge_id']);
                $linkedCharge = new LoanLinkedCharge();
                $linkedCharge->loan_id = $loan->id;
                $linkedCharge->name = $charge->name;
                $linkedCharge->loan_charge_id = $charge->id;
                if ($charge->allow_override == 1) {
                    $linkedCharge->amount = $key['amount'];
                } else {
                    $linkedCharge->amount = $charge->amount;
                }
                $linkedCharge->loan_charge_type_id = $charge->loan_charge_type_id;
                $linkedCharge->loan_charge_option_id = $charge->loan_charge_option_id;
                $linkedCharge->is_penalty = $charge->is_penalty;
                $linkedCharge->save();
            }
        }
        $loanHistory = new LoanHistory();
        $loanHistory->loan_id = $loan->id;
        $loanHistory->created_by_id = Auth::id();
        $loanHistory->user = Auth::user()->name;
        $loanHistory->action = 'Loan Edited';
        $loanHistory->save();
        if ($loan->loan_officer_id && $loan->wasChanged('loan_officer_id')) {
            $loanOfficerHistory = new LoanOfficerHistory();
            $loanOfficerHistory->loan_id = $loan->id;
            $loanOfficerHistory->created_by_id = Auth::id();
            $loanOfficerHistory->loan_officer_id = $request->loan_officer_id;
            $loanOfficerHistory->start_date = date("Y-m-d");
            $loanOfficerHistory->save();
        }
        return redirect()->route('loans.show', $loan->id)->with('success', 'Successfully updated');
    }

    public function changeStatus(Request $request, Loan $loan)
    {
        $oldStatus = $loan->status;
        if ($request->status === 'approved') {
            $loan->approved_by_user_id = Auth::id();
            $loan->approved_amount = $request->approved_amount;
            $loan->approved_on_date = date('Y-m-d');
            $loan->approved_notes = $request->description;
        }
        if ($request->status === 'rejected') {
            $loan->rejected_by_user_id = Auth::id();
            $loan->rejected_on_date = date("Y-m-d");
            $loan->rejected_notes = $request->description;
        }
        if ($request->status === 'withdrawn') {
            $loan->withdrawn_by_user_id = Auth::id();
            $loan->withdrawn_on_date = date("Y-m-d");
            $loan->withdrawn_notes = $request->description;
        }
        if ($request->status === 'written_off') {
            $loan->written_off_by_user_id = Auth::id();
            $loan->written_off_on_date = date("Y-m-d");
            $loan->written_off_notes = $request->description;
        }
        if ($request->status === 'active') {
            $loan->disbursed_by_user_id = Auth::id();
            $loan->disbursed_on_date = $request->disbursed_on_date;
            $loan->first_payment_date = $request->first_payment_date;
            $loan->principal = $loan->approved_amount;
            $loan->payment_type_id = $request->payment_type_id;
            $loan->disbursed_notes = $request->description;
            $loan->dis_bank_name = $request->bank_name;
            $loan->dis_account_holder_name = $request->account_holder_name;
            $loan->dis_bank_account = $request->bank_account;
            $loan->dis_branch_code = $request->branch_code;
        }
        $loan->status = $request->status;
        $loan->save();
        $loanHistory = new LoanHistory();
        $loanHistory->loan_id = $loan->id;
        $loanHistory->created_by_id = Auth::id();
        $loanHistory->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loanHistory->action = 'Loan Status changed from ' . $oldStatus . ' to ' . $loan->status;
        $loanHistory->save();
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $oldStatus));
        return redirect()->route('loans.show', $loan->id)->with('success', 'Successfully saved');
    }

    public function undoStatus(Request $request, Loan $loan)
    {
        $oldStatus = $loan->status;
        if ($request->status === 'undo_approval') {
            $loan->approved_by_user_id = null;
            $loan->approved_amount = null;
            $loan->approved_on_date = null;
            $loan->status = 'submitted';
            $loan->approved_notes = null;
        }
        if ($request->status === 'undo_rejection') {
            $loan->rejected_by_user_id = null;
            $loan->rejected_on_date = null;
            $loan->status = 'submitted';
            $loan->rejected_notes = null;
        }
        if ($request->status === 'undo_withdrawn') {
            $loan->withdrawn_by_user_id = null;
            $loan->withdrawn_on_date = null;
            $loan->status = 'submitted';
            $loan->withdrawn_notes = null;
        }
        if ($request->status === 'undo_write_off') {
            $loan->written_off_by_user_id = null;
            $loan->written_off_on_date = null;
            $loan->status = 'active';
            $loan->written_off_notes = null;
        }
        if ($request->status === 'undo_disbursement') {
            $loan->disbursed_by_user_id = null;
            $loan->disbursed_on_date = null;
            $loan->status = 'approved';
            $loan->disbursed_notes = null;
        }
        $loan->save();
        $loanHistory = new LoanHistory();
        $loanHistory->loan_id = $loan->id;
        $loanHistory->created_by_id = Auth::id();
        $loanHistory->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loanHistory->action = 'Loan Status changed from ' . $oldStatus . ' to ' . $loan->status;
        $loanHistory->save();
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $oldStatus));
        return redirect()->route('loans.show', $loan->id)->with('success', 'Successfully saved');
    }


    public function changeLoanOfficer(Request $request, $id)
    {
        $request->validate([
            'loan_officer_id' => ['required'],
        ]);
        $loan = Loan::find($id);
        $previous_loan_officer_id = $loan->loan_officer_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->save();
        if ($loan->wasChanged('loan_officer_id')) {
            $previous_loan_officer = LoanOfficerHistory::where('loan_id', $loan->id)->where('loan_officer_id', $request->loan_officer_id)->where('end_date', '')->first();
            if (!empty($previous_loan_officer)) {
                $previous_loan_officer->end_date = date("Y-m-d");
                $previous_loan_officer->save();
            }
            $loanOfficerHistory = new LoanOfficerHistory();
            $loanOfficerHistory->loan_id = $loan->id;
            $loanOfficerHistory->created_by_id = Auth::id();
            $loanOfficerHistory->loan_officer_id = $request->loan_officer_id;
            $loanOfficerHistory->start_date = date("Y-m-d");
            $loanOfficerHistory->save();
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Change Loan Officer');
        return redirect()->route('loans.show', $loan->id)->with('success', 'Successfully saved');
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */


    /**
     * Remove the specified resource from storage.
     * @param Loan $loan
     * @return RedirectResponse
     */
    public function destroy(Loan $loan)
    {
        if ($loan->status !== 'submitted') {
            return redirect()->back()->with('error', 'Successfully deleted');
        }
        $loan->charges()->delete();
        $loan->files()->delete();
        $loan->collateral()->delete();
        $loan->notes()->delete();
        $loan->guarantors()->delete();
        $loan->schedules()->delete();
        $loan->transactions()->delete();
        $loan->delete();
        return redirect()->route('loans.index', $loan->id)->with('success', 'Successfully deleted');
    }


    public function createLoanCalculator()
    {
        $products = LoanProduct::with(['charges', 'charges.charge', 'charges.charge.option', 'charges.charge.type'])->where('active', 1)->get();
        $purposes = LoanPurpose::get();
        return Inertia::render('Loans/Calculator', [
            'products' => $products,
            'purposes' => $purposes,
        ]);
    }

    public function processLoanCalculator(Request $request)
    {
        $product = LoanProduct::with(['charges', 'charges.charge', 'charges.charge.option', 'charges.charge.type'])->where('active', 1)->find($request->loan_product_id);
        $loan_details = [];
        $loan_details['principal'] = $request->applied_amount;
        $loan_details['disbursement_date'] = $request->expected_disbursement_date;
        $schedules = [];
        $loan_principal = $request->applied_amount;
        $interest_rate = determine_period_interest_rate($request->interest_rate, $request->repayment_frequency_type, $product->interest_rate_type);
        $balance = round($loan_principal, $product->decimals);
        $period = floor($request->loan_term / $request->repayment_frequency);
        $payment_from_date = $request->expected_disbursement_date;
        $next_payment_date = $request->expected_first_payment_date;
        $total_principal = 0;
        $total_interest = 0;
        $total_days = 0;
        for ($i = 1; $i <= $period; $i++) {
            $schedule = [];

            $schedule['installment'] = $i;

            $schedule['due_date'] = $next_payment_date;
            $schedule['from_date'] = $payment_from_date;
            $schedule['fees'] = 0;
            $schedule['days'] = Carbon::parse($schedule['due_date'])->diffInDays(Carbon::parse($schedule['from_date']));
            $total_days = $total_days + $schedule['days'];
            //flat  method
            if ($product->interest_methodology == 'flat') {
                $principal = round($loan_principal / $period, $product->decimals);
                $interest = round($interest_rate * $loan_principal, $product->decimals);
                if ($product->grace_on_interest_charged >= $i) {
                    $schedule['interest'] = 0;
                } else {
                    $schedule['interest'] = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $schedule['principal'] = round($balance, $product->decimals);
                } else {
                    $schedule['principal'] = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($product->interest_methodology == 'declining_balance') {
                if ($product->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $loan_principal, $period), $product->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $product->decimals);
                    $principal = round(($amortized_payment - $interest), $product->decimals);
                    if ($product->grace_on_interest_charged >= $i) {
                        $schedule['interest'] = 0;
                    } else {
                        $schedule['interest'] = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $schedule['principal'] = round($balance, $product->decimals);
                        $balance = 0;
                    } else {
                        $schedule['principal'] = $principal;
                        $balance = ($balance - $principal);
                    }
                }
                if ($product->amortization_method == 'equal_principal_payments') {
                    $principal = round($loan_principal / $period, $product->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $product->decimals);
                    if ($product->grace_on_interest_charged >= $i) {
                        $schedule['interest'] = 0;
                    } else {
                        $schedule['interest'] = $interest;
                    }

                    if ($i == $period) {
                        //account for values lost during rounding
                        $schedule['principal'] = round($balance, $product->decimals);
                        $balance = 0;
                    } else {
                        $schedule['principal'] = $principal;
                        $balance = ($balance - $principal);
                    }
                    //determine next balance

                }

            }
            $schedule['balance'] = (double)$balance;
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = Carbon::parse($next_payment_date)->add($product->repayment_frequency, $product->repayment_frequency_type)->format("Y-m-d");
            $total_principal = $total_principal + $schedule['principal'];
            $total_interest = $total_interest + $schedule['interest'];
            $schedules[] = $schedule;
        }

        $installment_fees = 0;
        $disbursement_fees = 0;
        foreach ($product->charges as $key) {
            //disbursement

            if ($key->charge->type->name === 'Disbursement') {
                $amount = 0;
                if ($key->charge->loan_charge_option_id == 1) {
                    $amount = $key->charge->amount;

                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $amount = round(($key->charge->amount * $total_interest / 100), $product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $amount = round(($key->charge->amount * $loan_principal / 100), $product->decimals);

                }
                $disbursement_fees = $disbursement_fees + $amount;
            }
            //installment_fee
            if ($key->charge->type->name === 'Installment Fees') {
                $amount = 0;
                if ($key->charge->option->name === 'Flat') {
                    $amount = $key->charge->amount;
                }
                if ($key->charge->option->name === 'Principal due on installment') {
                    $amount = round(($key->charge->amount * $total_principal / 100), $product->decimals);
                }
                if ($key->charge->option->name === 'Principal + Interest due on installment') {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $product->decimals);
                }
                if ($key->charge->option->name === 'Interest due on installment') {
                    $amount = round(($key->charge->amount * $total_interest / 100), $product->decimals);
                }
                if ($key->charge->option->name === 'Total Outstanding Loan Principal') {
                    $amount = round(($key->charge->amount * $total_principal / 100), $product->decimals);
                }
                if ($key->charge->option->name === 'Percentage of Original Loan Principal per Installment') {
                    $amount = round(($key->charge->amount * $total_principal / 100), $product->decimals);
                }
                if ($key->charge->option->name === 'Original Loan Principal') {
                    $amount = round(($key->charge->amount * $loan_principal / 100), $product->decimals);
                }
                $installment_fees = $installment_fees + $amount;
                //add the charges to the schedule
                foreach ($schedules as &$temp) {
                    if ($key->charge->option->name === 'Principal due on installment') {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['principal'] / 100), $product->decimals);
                    } elseif ($key->charge->option->name === 'Principal + Interest due on installment') {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * ($temp['interest'] + $temp['principal']) / 100), $product->decimals);
                    } elseif ($key->charge->option->name === 'Interest due on installment') {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['interest'] / 100), $product->decimals);
                    } else {
                        $temp['fees'] = $temp['fees'] + $key->charge->amount;
                    }

                }

            }

        }
        foreach ($schedules as $key => $value) {
            $schedules[$key]['total_due'] = $value['principal'] + $value['interest'] + $value['fees'];
        }
        $loan_details['total_days'] = $total_days;
        $loan_details['total_principal'] = $total_principal;
        $loan_details['total_interest'] = $total_interest;
        $loan_details['decimals'] = $product->decimals;
        $loan_details['disbursement_fees'] = $disbursement_fees;
        $loan_details['total_fees'] = $disbursement_fees + $installment_fees;
        $loan_details['total_due'] = $disbursement_fees + $installment_fees + $total_interest + $total_principal;
        $loan_details['maturity_date'] = $next_payment_date;
        activity()->log('Use Loan Calculator');
        return Inertia::render('Loans/CalculatorResults', [
            'loan_details' => $loan_details,
            'schedules' => $schedules
        ]);
    }


}
