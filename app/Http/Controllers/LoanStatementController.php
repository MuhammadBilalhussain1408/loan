<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\Member;
use App\Models\PaymentType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoanStatementController extends Controller
{
    public function index(Request $request)
    {
        $member = Member::where('id',$request->member_id)->first();
        $loan = Loan::where('member_id', $request->member_id)->latest()->first();
        // dd($loan);
        if ($loan) {
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
            return Inertia::render('LoanStatement/index', [
                'loan' => $loan,
                'results' => $results,
                'paymentTypes' => PaymentType::where('active', 1)->get(),
                'CurrentMember'=>$member
            ]);
        }else{
            return Inertia::render('LoanStatement/index', [
                'loan' => [],
                'results' => [],
                'paymentTypes' => PaymentType::where('active', 1)->get(),
                'CurrentMember'=>''
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function backup(Request $request)
    {
        $LoanStatement = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $duration = $request->duration;
        switch ($duration) {
            case 'This Month':
                $start_date = Carbon::now()->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
                break;
            case 'Previous Month':
                $start_date = Carbon::now()->subMonth()->startOfMonth();
                $end_date = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'This Year':
                $start_date = Carbon::now()->startOfYear();
                $end_date = Carbon::now()->endOfYear();
                break;
            case 'Previous Year':
                $start_date = Carbon::now()->subYear()->startOfYear();
                $end_date = Carbon::now()->subYear()->endOfYear();
                break;
        }
        if ($request->member_id && $start_date && $end_date) {
            $LoanStatement = LoanApplication::where('member_id', $request->member_id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->get();
        }
        if ($request->member_id && is_null($LoanStatement)) {
            $LoanStatement = LoanApplication::where('member_id', $request->member_id)->get();
        }

        return Inertia::render('LoanStatement/index', [
            'LoanStatement' => $LoanStatement
        ]);
    }
    function getLoanStatement(Request $request)
    {
        dd($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
