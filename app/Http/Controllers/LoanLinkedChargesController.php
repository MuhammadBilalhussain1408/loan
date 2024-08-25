<?php

namespace App\Http\Controllers;

use App\Events\TransactionUpdated;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanLinkedCharge;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;

use App\Models\PaymentType;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;


class LoanLinkedChargesController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.transactions.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.transactions.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.transactions.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.transactions.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Loan $loan)
    {
        $loan->load(['member', 'product', 'currency', 'loanOfficer', 'purpose', 'fund']);
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
        $results = LoanLinkedCharge::with(['charge', 'charge.type', 'charge.option'])
            ->where('loan_id', $loan->id)
            ->orderBy('id')
            ->paginate();
        return Inertia::render('Loans/Charges/Index', [
            'loan' => $loan,
            'results' => $results,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create(Loan $loan)
    {
        $loan->load(['product', 'product.charges', 'product.charges.charge', 'product.charges.charge.type', 'product.charges.charge.option']);
        return Inertia::render('Loans/Charges/Create', [
            'loan' => $loan,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, Loan $loan)
    {

        $request->validate([
            'amount' => ['required'],
            'loan_charge_id' => ['required'],
            'date' => ['required', 'date'],
        ]);
        $charge = LoanCharge::find($request->loan_charge_id);
        $linkedCharge = new LoanLinkedCharge();
        $linkedCharge->loan_id = $loan->id;
        $linkedCharge->name = $charge->name;
        $linkedCharge->loan_charge_id = $charge->id;
        if ($charge->allow_override == 1) {
            $linkedCharge->amount = $request->amount;
        } else {
            $linkedCharge->amount = $charge->amount;
        }
        $linkedCharge->loan_charge_type_id = $charge->loan_charge_type_id;
        $linkedCharge->loan_charge_option_id = $charge->loan_charge_option_id;
        $linkedCharge->is_penalty = $charge->is_penalty;
        $linkedCharge->save();
        //find schedule to apply this charge
        $repayment_schedule = $loan->schedules->where('due_date', '>=', $request->date)->where('from_date', '<=', $request->date)->first();
        if (empty($repayment_schedule)) {
            if (Carbon::parse($request->date)->lessThan($loan->first_payment_date)) {
                $repayment_schedule = $loan->schedules->first();
            } else {
                $repayment_schedule = $loan->schedules->last();
            }

        }
        //calculate the amount
        if ($linkedCharge->charge->option->name ==='Flat') {
            $amount = $linkedCharge->amount;
        }
        if ($linkedCharge->charge->option->name ==='Principal due on installment') {
            $amount = round(($linkedCharge->amount * ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived) / 100), $loan->decimals);
        }
        if ($linkedCharge->charge->option->name ==='Principal + Interest due on installment') {
            $amount = round(($linkedCharge->amount * (($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) + ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived)) / 100), $loan->decimals);
        }
        if ($linkedCharge->charge->option->name ==='Interest due on installment') {
            $amount = round(($linkedCharge->amount * ($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) / 100), $loan->decimals);
        }
        if ($linkedCharge->charge->option->name ==='Total Outstanding Loan Principal') {
            $amount = round(($linkedCharge->amount * ($loan->schedules->sum('principal') - $loan->schedules->sum('principal_repaid_derived') - $loan->schedules->sum('principal_written_off_derived')) / 100), $loan->decimals);
        }
        if ($linkedCharge->charge->option->name ==='Percentage of Original Loan Principal per Installment') {
            $amount = round(($linkedCharge->amount * $loan->principal / 100), $loan->decimals);
        }
        if ($linkedCharge->charge->option->name ==='Original Loan Principal') {
            $amount = round(($linkedCharge->amount * $loan->principal / 100), $loan->decimals);
        }
        $repayment_schedule->fees = $repayment_schedule->fees + $amount;
        $repayment_schedule->save();
        $linkedCharge->calculated_amount = $amount;
        $linkedCharge->due_date = $repayment_schedule->due_date;
        //create transaction
        $transaction = new LoanTransaction();
        $transaction->created_by_id = Auth::id();
        $transaction->loan_id = $loan->id;
        $transaction->name = 'Fee Applied';
        $transaction->loan_transaction_type_id = LoanTransactionType::where('name','Apply Charges')->first()->id;
        $transaction->submitted_on = $repayment_schedule->due_date;
        $transaction->created_on = date("Y-m-d");
        $transaction->amount = $linkedCharge->calculated_amount;
        $transaction->due_date = $repayment_schedule->due_date;
        $transaction->debit = $linkedCharge->calculated_amount;
        $transaction->reversible = 1;
        $transaction->save();
        $linkedCharge->loan_transaction_id = $transaction->id;
        $linkedCharge->save();
        activity()->on($charge)
            ->withProperties(['id' => $charge->id])
            ->log('Create Loan Charge');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        return redirect()->route('loans.linked_charges.index', $loan->id)->with('success', 'Successfully created');
    }

    public function waive(Request $request, LoanLinkedCharge $linkedCharge)
    {

        $linkedCharge->waived = 1;
        $linkedCharge->save();
        $loan = $linkedCharge->loan;
        $transaction = $linkedCharge->transaction;
        $transaction->credit = $transaction->amount;
        $transaction->debit = $transaction->amount;
        $transaction->reversed = 1;
        $transaction->save();
        if ($linkedCharge->charge->type->name ==='Specified Due Date' || $linkedCharge->charge->type->name ==='Overdue Installment Fee' || $linkedCharge->charge->type->name ==='Disbursement - Paid With Repayment' ||$linkedCharge->charge->type->name ==='Specified Due Date' || $linkedCharge->charge->type->name ==='Overdue On Loan Maturity' ||$linkedCharge->charge->type->name ==='Last installment fee') {
            $repayment_schedule = LoanRepaymentSchedule::where('loan_id', $loan->id)->where('due_date', $transaction->due_date)->first();
            if ($linkedCharge->is_penalty == 1) {
                $repayment_schedule->penalties_waived_derived = $repayment_schedule->penalties_waived_derived + $linkedCharge->calculated_amount;
            } else {
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $linkedCharge->calculated_amount;
            }
            $repayment_schedule->save();
        }
        if ($linkedCharge->charge->type->name ==='Installment Fees') {
            $amount = 0;
            foreach ($loan->repayment_schedules as $repayment_schedule) {
                if ($linkedCharge->charge->option->name ==='Flat') {
                    $amount = $linkedCharge->amount;
                }
                if ($linkedCharge->charge->option->name ==='Principal due on installment') {
                    $amount = round(($linkedCharge->amount * ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived) / 100), $loan->decimals);
                }
                if ($linkedCharge->charge->option->name ==='Principal + Interest due on installment') {
                    $amount = round(($linkedCharge->amount * (($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) + ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived)) / 100), $loan->decimals);
                }
                if ($linkedCharge->charge->option->name ==='Interest due on installment') {
                    $amount = round(($linkedCharge->amount * ($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) / 100), $loan->decimals);
                }
                if ($linkedCharge->charge->option->name ==='Total Outstanding Loan Principal') {
                    $amount = round(($linkedCharge->amount * ($loan->schedules->sum('principal') - $loan->schedules->sum('principal_repaid_derived') - $loan->schedules->sum('principal_written_off_derived')) / 100), $loan->decimals);
                }
                if ($linkedCharge->charge->option->name ==='Percentage of Original Loan Principal per Installment') {
                    $amount = round(($linkedCharge->amount * $loan->principal / 100), $loan->decimals);
                }
                if ($linkedCharge->charge->option->name ==='Original Loan Principal') {
                    $amount = round(($linkedCharge->amount * $loan->principal / 100), $loan->decimals);
                }
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $amount;
                $repayment_schedule->save();
            }
        }
        activity()->on($linkedCharge)
            ->withProperties(['id' => $linkedCharge->id])
            ->log('Waive Loan Charge');
        //fire transaction updated event
        event(new TransactionUpdated($linkedCharge->loan));
        return redirect()->route('loans.linked_charges.index', $linkedCharge->loan_id)->with('success', 'Successfully saved');
    }
}

