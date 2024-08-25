<?php

namespace App\Http\Controllers\MemberPortal;

use App\Http\Controllers\FilesController;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Inertia\Inertia;


class MemberPortalLoanCollateralController extends Controller
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
        $results = LoanCollateral::with(['createdBy', 'type', 'file'])
            ->where('loan_id', $loan->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('MemberPortal/Loans/Collateral/Index', [
            'loan' => $loan,
            'results' => $results,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }




    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanCollateral $collateral
     * @return RedirectResponse
     */
    public function update(Request $request, LoanCollateral $collateral)
    {
        $request->validate([
            'loan_collateral_type_id' => ['required'],
            'file' => ['file', 'nullable', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        $collateral->loan_collateral_type_id = $request->loan_collateral_type_id;
        $collateral->value = $request->value;
        $collateral->description = $request->description;
        $collateral->save();
        if ($request->hasFile('file')) {
            $fileController = new FilesController();
            if ($collateral->file) {
                $fileController->destroy($collateral->file->id);
            }
            $file = $fileController->store([
                'file' => $request->file('file'),
                'name' => $request->name,
                'description' => $request->description,
                'category' => 'loan_collateral',
                'record_id' => $collateral->loan_id,
            ]);
            $collateral->link = $file->link;
            $collateral->save();
        }

        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $collateral->loan_id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $collateral->loan_id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        return redirect()->route('loans.collaterals.index', $collateral->loan_id)->with('success', 'Successfully deleted');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanCollateral $collateral
     * @return RedirectResponse
     */
    public function destroy(LoanCollateral $collateral)
    {
        $collateral->delete();
        return redirect()->route('loans.collaterals.index', $collateral->loan_id)->with('success', 'Successfully deleted');
    }
}
