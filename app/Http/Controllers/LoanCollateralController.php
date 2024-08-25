<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\LoanCollateralType;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class LoanCollateralController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.collateral.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.collateral.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.collateral.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.collateral.destroy'])->only(['destroy']);

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
        $results = LoanCollateral::with(['createdBy', 'type', 'file'])
            ->where('loan_id', $loan->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Loans/Collateral/Index', [
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

        return Inertia::render('Loans/Collateral/Create', [
            'loan' => $loan,
            'types' => LoanCollateralType::all(),
            'customFields' => CustomField::where('category', 'collateral')->where('active', 1)->get()
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
            'loan_collateral_type_id' => ['required'],
            'file' => ['file', 'nullable', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        $collateral = new LoanCollateral();
        $collateral->created_by_id = Auth::id();
        $collateral->loan_id = $loan->id;
        $collateral->loan_collateral_type_id = $request->loan_collateral_type_id;
        $collateral->value = $request->value;
        $collateral->description = $request->description;
        $collateral->save();
        if ($request->hasFile('file')) {
            $fileController = new FilesController();
            $file = $fileController->store([
                'file' => $request->file('file'),
                'name' => $request->name,
                'description' => $request->description,
                'category' => 'loan_collateral',
                'record_id' => $loan->id,
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
                ['category' => $field['category'], 'parent_id' => $loan->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $loan->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        return redirect()->route('loans.collaterals.index', $loan->id)->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanCollateral $collateral
     * @return \Inertia\Response
     */
    public function edit(LoanCollateral $collateral)
    {
        $customFields = CustomField::where('category', 'loan_collateral')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($collateral) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'loan_collateral')->where('parent_id', $collateral->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $collateral->custom_fields = $customFields;
        return Inertia::render('Loans/Collateral/Edit', [
            'loan' => $collateral->loan,
            'types' => LoanCollateralType::all(),
            'collateral' => $collateral,
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
