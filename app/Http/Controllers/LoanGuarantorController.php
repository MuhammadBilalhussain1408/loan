<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Member;
use App\Models\ClientRelationship;
use App\Models\FileType;
use App\Models\Country;
use App\Models\Loan;
use App\Models\LoanGuarantor;
use App\Models\PaymentType;
use App\Models\Profession;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoanGuarantorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.guarantors.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.guarantors.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.guarantors.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.guarantors.destroy'])->only(['destroy']);

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
        $results = LoanGuarantor::with(['createdBy', 'relationship', 'member'])
            ->where('loan_id', $loan->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Loans/Guarantors/Index', [
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

        return Inertia::render('Loans/Guarantors/Create', [
            'titles' => Title::all(),
            'professions' => Profession::all(),
            'types' => FileType::all(),
            'branches' => Branch::all(),
            'countries' => Country::all(),
            'relationships' => ClientRelationship::all(),
            'loan' => $loan,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Loan $loan)
    {
        $request->validate([
            'first_name' => ['required_if:is_member,false'],
            'last_name' => ['required_if:is_member,false'],
            'gender' => ['nullable', 'required_if:is_member,false'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'dob' => ['nullable', 'required_if:is_member,false', 'date'],
            'member_relationship_id' => ['nullable', 'required_if:is_member,false'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);
        $member = Member::find($request->member_id);
        $guarantor = new LoanGuarantor();
        $guarantor->created_by_id = Auth::id();
        $guarantor->loan_id = $loan->id;
        $guarantor->member_id = $request->member_id;
        $guarantor->is_member = $request->is_member;
        $guarantor->member_relationship_id = $request->member_relationship_id;
        (!empty($member)) ? $guarantor->first_name = $member->first_name : $guarantor->first_name = $request->first_name;
        (!empty($member)) ? $guarantor->last_name = $member->last_name : $guarantor->last_name = $request->last_name;
        (!empty($member)) ? $guarantor->gender = $member->gender : $guarantor->gender = $request->gender;
        (!empty($member)) ? $guarantor->country_id = $member->country_id : $guarantor->country_id = $request->country_id;
        (!empty($member)) ? $guarantor->branch_id = $member->branch_id : $guarantor->branch_id = $request->branch_id;
        (!empty($member)) ? $guarantor->title_id = $member->title_id : $guarantor->title_id = $request->title_id;
        (!empty($member)) ? $guarantor->profession_id = $member->profession_id : $guarantor->profession_id = $request->profession_id;
        (!empty($member)) ? $guarantor->mobile = $member->mobile : $guarantor->mobile = $request->mobile;
        (!empty($member)) ? $guarantor->notes = $member->notes : $guarantor->notes = $request->notes;
        (!empty($member)) ? $guarantor->email = $member->email : $guarantor->email = $request->email;
        (!empty($member)) ? $guarantor->address = $member->address : $guarantor->address = $request->address;
        (!empty($member)) ? $guarantor->marital_status = $member->marital_status : $guarantor->marital_status = $request->marital_status;
        (!empty($member)) ? $guarantor->dob = $member->dob : $guarantor->dob = $request->dob;
        $guarantor->guaranteed_amount = $request->guaranteed_amount;
        $guarantor->save();
        if ($request->file('photo')) {
            $member->updateProfilePhoto($request->file('photo'));
        }
        return redirect()->route('loans.guarantors.index', $loan->id)->with('success', 'Successfully created');
    }

    /**
     * Show the specified resource.
     * @param LoanGuarantor $guarantor
     * @return \Inertia\Response
     */
    public function show(LoanGuarantor $guarantor)
    {
        $guarantor->load(['member', 'relationship', 'country', 'title', 'profession']);
        return Inertia::render('Loans/Guarantors/Show', [
            'loan' => $guarantor->loan,
            'guarantor' => $guarantor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param LoanGuarantor $guarantor
     * @return \Inertia\Response
     */
    public function edit(LoanGuarantor $guarantor)
    {
        return Inertia::render('Loans/Guarantors/Edit', [
            'titles' => Title::all(),
            'professions' => Profession::all(),
            'types' => FileType::all(),
            'branches' => Branch::all(),
            'countries' => Country::all(),
            'relationships' => ClientRelationship::all(),
            'loan' => $guarantor->loan,
            'guarantor' => $guarantor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanGuarantor $guarantor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LoanGuarantor $guarantor)
    {
        $request->validate([
            'first_name' => ['required_if:is_member,false'],
            'last_name' => ['required_if:is_member,false'],
            'gender' => ['nullable', 'required_if:is_member,false'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'dob' => ['nullable', 'required_if:is_member,false', 'date'],
            'member_relationship_id' => ['required_if:is_member,false'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);
        $member = Member::find($request->member_id);
        $guarantor->member_id = $request->member_id;
        $guarantor->member_relationship_id = $request->member_relationship_id;
        (!empty($member)) ? $guarantor->first_name = $member->first_name : $guarantor->first_name = $request->first_name;
        (!empty($member)) ? $guarantor->last_name = $member->last_name : $guarantor->last_name = $request->last_name;
        (!empty($member)) ? $guarantor->gender = $member->gender : $guarantor->gender = $request->gender;
        (!empty($member)) ? $guarantor->country_id = $member->country_id : $guarantor->country_id = $request->country_id;
        (!empty($member)) ? $guarantor->branch_id = $member->branch_id : $guarantor->branch_id = $request->branch_id;
        (!empty($member)) ? $guarantor->title_id = $member->title_id : $guarantor->title_id = $request->title_id;
        (!empty($member)) ? $guarantor->profession_id = $member->profession_id : $guarantor->profession_id = $request->profession_id;
        (!empty($member)) ? $guarantor->mobile = $member->mobile : $guarantor->mobile = $request->mobile;
        (!empty($member)) ? $guarantor->notes = $member->notes : $guarantor->notes = $request->notes;
        (!empty($member)) ? $guarantor->email = $member->email : $guarantor->email = $request->email;
        (!empty($member)) ? $guarantor->address = $member->address : $guarantor->address = $request->address;
        (!empty($member)) ? $guarantor->marital_status = $member->marital_status : $guarantor->marital_status = $request->marital_status;
        (!empty($member)) ? $guarantor->dob = $member->dob : $guarantor->dob = $request->dob;
        $guarantor->guaranteed_amount = $request->guaranteed_amount;
        $guarantor->save();
        if ($request->file('photo')) {
            $member->updateProfilePhoto($request->file('photo'));
        }
        return redirect()->route('loans.guarantors.index', $guarantor->loan_id)->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanGuarantor $guarantor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LoanGuarantor $guarantor)
    {
        $guarantor->delete();
        return redirect()->route('loans.guarantors.index', $guarantor->loan_id)->with('success', 'Successfully deleted');
    }
}
