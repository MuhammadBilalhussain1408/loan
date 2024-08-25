<?php

namespace App\Http\Controllers\MemberPortal;

use App\Http\Controllers\FilesController;
use App\Models\File;
use App\Models\Loan;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MemberPortalLoanFileController extends Controller
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
        if ($loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
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
        $files = File::where('record_id', $loan->id)
            ->where('category', 'loans')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('MemberPortal/Loans/Files/Index', [
            'loan' => $loan,
            'files' => $files,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Loan $loan)
    {
        return Inertia::render('MemberPortal/Loans/Files/Create', [
            'loan' => $loan,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, Loan $loan)
    {

        $fileController = new FilesController();
        $file = $fileController->store([
            'file' => $request->file('file'),
            'name' => $request->name,
            'description' => $request->description,
            'category' => 'loans',
            'record_id' => $loan->id,
        ]);

        return redirect()->route('loans.files.index', $loan->id)->with('success', 'Loan File created successfully.');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param File $file
     * @return \Inertia\Response
     */
    public function edit(File $file)
    {
        $loan = Loan::find($file->record_id);

        return Inertia::render('Loans/Files/Edit', [
            'loan' => $loan,
            'file' => $file,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param File $file
     * @return RedirectResponse
     */
    public function update(Request $request, File $file)
    {

        $loan = Loan::find($file->record_id);
        $fileController = new FilesController();
        $file = $fileController->update($file->id, [
            'file' => $request->file('file'),
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return redirect()->route('loans.files.index', $loan->id)->with('success', 'Updated File successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param File $file
     * @return RedirectResponse
     */
    public function destroy(File $file)
    {

        $fileController = new FilesController();
        $fileController->destroy($file->id);
        return redirect()->route('loans.files.index', $file->record_id)->with('success', 'File deleted successfully.');

    }
}
