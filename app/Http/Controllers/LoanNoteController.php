<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanNote;
use App\Models\Note;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.notes.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.notes.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.notes.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.notes.destroy'])->only(['destroy']);

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
        $results = Note::with(['createdBy'])
            ->where('record_id', $loan->id)
            ->where('category', 'loan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Loans/Notes/Index', [
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
        $loan->load(['member', 'product', 'currency', 'loanOfficer', 'purpose', 'fund']);
        return Inertia::render('Loans/Notes/Create', [
            'loan' => $loan
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
            'description' => ['required'],
        ]);
        $note = new Note();
        $note->created_by_id = Auth::id();
        $note->record_id = $loan->id;
        $note->category = 'loan';
        $note->description = $request->description;
        $note->save();
        return redirect()->route('loans.notes.index', $loan->id)->with('success', 'Successfully created.');
    }


    /**
     * Show the form for editing the specified resource.
     * @param Note $note
     * @return \Inertia\Response
     */
    public function edit(Note $note)
    {
        $loan = Loan::find($note->record_id);
        return Inertia::render('Loans/Notes/Edit', [
            'note' => $note,
            'loan' => $loan
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Note $note
     * @return RedirectResponse
     */
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'description' => ['required'],
        ]);
        $note->description = $request->description;
        $note->save();
        return redirect()->route('loans.notes.index', $note->record_id)->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param Note $note
     * @return RedirectResponse
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('loans.notes.index', $note->record_id)->with('success', 'Successfully deleted');
    }
}
