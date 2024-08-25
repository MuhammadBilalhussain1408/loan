<?php

namespace App\Http\Controllers\MemberPortal;

use App\Models\Loan;
use App\Models\PaymentType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class MemberPortalLoanScheduleController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Loan $loan)
    {
        dd($loan);
        if ($loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        $loan->load(['member', 'product', 'currency', 'loanOfficer', 'purpose', 'fund', 'schedules']);
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
        return Inertia::render('MemberPortal/Loans/Schedules/Index', [
            'loan' => $loan,
            'totalPrincipal' => $loan->schedules->sum('principal') - $loan->schedules->sum('principal_written_off_derived'),
            'totalInterest' => $loan->schedules->sum('interest') - $loan->schedules->sum('interest_waived_derived') - $loan->schedules->sum('interest_written_off_derived'),
            'totalFees' => $loan->schedules->sum('fees') - $loan->schedules->sum('fees_waived_derived') - $loan->schedules->sum('fees_written_off_derived'),
            'totalPenalties' => $loan->schedules->sum('penalties') - $loan->schedules->sum('penalties_waived_derived') - $loan->schedules->sum('penalties_written_off_derived'),
            'totalPaid' => $loan->schedules->sum('principal_repaid_derived') + $loan->schedules->sum('interest_repaid_derived') + $loan->schedules->sum('penalties_written_off_derived') + $loan->schedules->sum('fees_repaid_derived'),
            'totalDue' => $loan->schedules->sum('total_due'),
            'totalDays' => $loan->schedules->sum('days'),
            'totalAmount' => $loan->schedules->sum('total'),
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }


    public function email(Loan $loan)
    {
        if ($loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        $loan->load(['schedules', 'member', 'product']);
        //return Inertia::render('loan::loan_schedule.email', compact('loan'));
    }

    public function pdf(Loan $loan)
    {
        if ($loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        $loan->load(['schedules', 'member', 'product']);
        $pdf = Pdf::loadView('loan_schedule.pdf', [
            'loan' => $loan
        ])->setPaper('a4', 'landscape');
        return $pdf->download("repayment_schedule.pdf");
    }

    public function printSchedule(Loan $loan)
    {
        if ($loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        $loan->load(['schedules', 'member', 'product']);
        return view('loan_schedule.print', [
            'loan' => $loan
        ]);
    }

}
