<?php

namespace App\Http\Controllers;

use App\Events\LoanTransactionCreated;
use App\Events\TransactionUpdated;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\PaymentDetail;
use App\Models\PaymentType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;


class LoanTransactionController extends Controller
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
        $results = LoanTransaction::with(['createdBy', 'type', 'createdBy', 'paymentDetail'])
            ->where('loan_id', $loan->id)
            ->orderBy('id')
            ->get();
        $balance = $loan->principal;
        $results->transform(function ($item) use (&$balance) {
            if ($item->type->name === 'Apply Charges' || $item->type->name === 'Apply Interest') {
                $balance = $balance + $item->amount;
            }
            if ($item->type->name === 'Repayment' || $item->type->name === 'Waive Interest' || $item->type->name === 'Recovery Repayment' || $item->type->name === 'Waive Charges' || $item->type->name === 'Write Off') {
                $balance = $balance - $item->amount;
            }
            $item->balance = $balance;
            return $item;
        });
        return Inertia::render('Loans/Transactions/Index', [
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

        return Inertia::render('Loans/Transactions/Create', [
            'loan' => $loan,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
            'customFields' => CustomField::where('category', 'repayment')->where('active', 1)->get()->transform(function ($item) {
                $item->field_value = '';
                return $item;
            })
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
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        //payment details
        $paymentDetail = new PaymentDetail();
        $paymentDetail->created_by_id = Auth::id();
        $paymentDetail->payment_type_id = $request->payment_type_id;
        $paymentDetail->transaction_type = 'loan_transaction';
        $paymentDetail->cheque_number = $request->cheque_number;
        $paymentDetail->receipt = $request->receipt;
        $paymentDetail->account_number = $request->account_number;
        $paymentDetail->bank_name = $request->bank_name;
        $paymentDetail->routing_code = $request->routing_code;
        $paymentDetail->description = $request->description;
        $paymentDetail->save();
        $transaction = new LoanTransaction();
        $transaction->created_by_id = Auth::id();
        $transaction->loan_id = $loan->id;
        $transaction->payment_detail_id = $paymentDetail->id;
        $transaction->name = 'Repayment';
        $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Repayment')->first()->id;
        $transaction->submitted_on = $request->date;
        $transaction->created_on = date("Y-m-d");
        $transaction->amount = $request->amount;
        $transaction->credit = $request->amount;
        $transaction->reversible = 1;
        $transaction->save();
        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $transaction->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $transaction->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        activity()->on($transaction)
            ->withProperties(['id' => $transaction->id, 'amount' => $request->amount, 'date' => $request->date])
            ->log('Create Loan Repayment');
        //fire transaction updated event
        event(new LoanTransactionCreated($transaction));
        event(new TransactionUpdated($loan));
        return redirect()->route('loans.transactions.index', $loan->id)->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanTransaction $transaction
     * @return \Inertia\Response
     */
    public function show(LoanTransaction $transaction)
    {
        $transaction->load(['paymentDetail', 'paymentDetail.payment_type', 'type', 'createdBy']);
        $customFields = CustomField::where('category', 'repayment')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($transaction) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'repayment')->where('parent_id', $transaction->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $transaction->custom_fields = $customFields;
        return Inertia::render('Loans/Transactions/Show', [
            'loan' => $transaction->loan,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param LoanTransaction $transaction
     * @return \Inertia\Response
     */
    public function edit(LoanTransaction $transaction)
    {
        $transaction->load(['paymentDetail']);
        $customFields = CustomField::where('category', 'repayment')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($transaction) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'repayment')->where('parent_id', $transaction->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $transaction->custom_fields = $customFields;
        return Inertia::render('Loans/Transactions/Edit', [
            'loan' => $transaction->loan,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanTransaction $transaction
     * @return RedirectResponse
     */
    public function update(Request $request, LoanTransaction $transaction)
    {
        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $oldAmount = $transaction->amount;
        $paymentDetail = $transaction->paymentDetail;
        $paymentDetail->payment_type_id = $request->payment_type_id;
        $paymentDetail->cheque_number = $request->cheque_number;
        $paymentDetail->receipt = $request->receipt;
        $paymentDetail->account_number = $request->account_number;
        $paymentDetail->bank_name = $request->bank_name;
        $paymentDetail->routing_code = $request->routing_code;
        $paymentDetail->description = $request->description;
        $paymentDetail->save();
        $transaction->submitted_on = $request->date;
        $transaction->amount = $request->amount;
        $transaction->credit = $request->amount;
        $transaction->save();

        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $transaction->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $transaction->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        activity()->on($transaction)
            ->withProperties(['id' => $transaction->id, 'amount' => $request->amount, 'old_amount' => $oldAmount])
            ->log('Update Loan Repayment');
        event(new TransactionUpdated($transaction->loan));
        return redirect()->route('loans.transactions.index', $transaction->loan_id)->with('success', 'Successfully deleted');
    }

    public function reverse(Request $request, LoanTransaction $transaction)
    {


        $transaction->amount = 0;
        $transaction->debit = $transaction->credit;
        $transaction->reversed = 1;
        $transaction->reversible = 0;
        $transaction->save();
        activity()->on($transaction)
            ->withProperties(['id' => $transaction->id])
            ->log('Reverse Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($transaction->loan));
        return redirect()->route('loans.transactions.index', $transaction->loan_id)->with('success', 'Successfully saved');
    }

    public function pdf(LoanTransaction $transaction)
    {
        $transaction->load(['paymentDetail', 'paymentDetail.payment_type', 'loan', 'type', 'createdBy']);
        $pdf = Pdf::loadView('loan_transaction.pdf', [
            'transaction' => $transaction
        ]);
        return $pdf->download("transaction.pdf");
    }

    public function printTransaction(LoanTransaction $transaction)
    {
        $transaction->load(['paymentDetail', 'paymentDetail.payment_type', 'loan', 'type', 'createdBy']);
        return view('loan_transaction.print', [
            'transaction' => $transaction
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanTransaction $collateral
     * @return RedirectResponse
     */
    public function destroy(LoanTransaction $collateral)
    {
        $collateral->delete();
        return redirect()->route('loans.collaterals.index', $collateral->loan_id)->with('success', 'Successfully deleted');
    }
}

