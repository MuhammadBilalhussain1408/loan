<?php

namespace App\Http\Controllers;

use App\Imports\MemberContributionImport;
use App\Models\MemberContribution;
use App\Models\Currency;
use App\Models\CustomField;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanApplicationApprovalStage;
use App\Models\LoanApplicationChecklist;
use App\Models\LoanProduct;
use App\Models\LoanPurpose;
use App\Models\MemberCategory;
use App\Models\MemberDesignation;
use App\Models\StopLoan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class MemberContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';
        $start_date = null;
        $end_date = null;
        $results = null;
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
        if ($request->member_id && $start_date && $end_date && $request->member_category) {
            $results = MemberContribution::where('member_id', $request->member_id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->where('member_category', $request->member_category)
                ->paginate($perPage)
                ->appends($request->input());
        }
        if ($request->member_id && $start_date && $end_date && is_null($results)) {
            $results = MemberContribution::where('member_id', $request->member_id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->paginate($perPage)
                ->appends($request->input());
        }
        if ($request->member_id && $request->member_category  && is_null($results)) {
            $results = MemberContribution::where('member_id', $request->member_id)
                ->where('member_category', $request->member_category)
                ->paginate($perPage)
                ->appends($request->input());
        }
        if ($request->member_category && $start_date && $end_date && is_null($results)) {
            $results = MemberContribution::where('member_category', $request->member_category)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->paginate($perPage)
                ->appends($request->input());
        }
        if ($request->member_category && is_null($results)) {
            $results = MemberContribution::where('member_category', $request->member_category)
                ->paginate($perPage)
                ->appends($request->input());
        }
        if ($request->member_id && is_null($results)) {
            $results = MemberContribution::where('member_id', $request->member_id)
                ->paginate($perPage)
                ->appends($request->input());
        }
        if (is_null($results)) {
            $results = MemberContribution::paginate($perPage)
                ->appends($request->input());
        }
        $memberCategories = MemberCategory::all();
        // $products = LoanProduct::where('active', 1)->get();
        $currencies = Currency::where('active', 1)->get();
        $approvalStages = LoanApplicationApprovalStage::get();
        return Inertia::render('Contribution/index', [
            'filters' => \request()->all('search', 'status', 'member_id', 'loan_product_id', 'currency_id', 'branch_id', 'current_stage_id', 'next_stage_id'),
            'results' => $results,
            'currencies' => $currencies,
            'approvalStages' => $approvalStages,
            'memberCategories' => $memberCategories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = LoanProduct::with(['charges', 'charges.charge', 'charges.charge.option', 'charges.charge.type'])->where('active', 1)->get();
        $checklists = LoanApplicationChecklist::all();
        $purposes = LoanPurpose::get();
        $approvalStages = LoanApplicationApprovalStage::get();
        $customFields = CustomField::where('category', 'loan_application')->where('active', 1)->get()->transform(function ($item) {
            $item->field_value = '';
            return $item;
        });
        return Inertia::render('Contribution/create', [
            'member_id' => request('member_id'),
            'products' => $products,
            'checklists' => $checklists,
            'purposes' => $purposes,
            'approvalStages' => $approvalStages,
            'designations' => MemberDesignation::all(),
            'categories' => MemberCategory::all(),
            'customFields' => $customFields
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->except('member_type'));
        $arr = $request->except('member_type', 'surname');
        $arr['total_contribution'] = $arr['contri_15_per'] + $arr['contri_30_per'];
        $oldMemContribution = MemberContribution::where('member_id', $arr['member_id'])->latest()->first();
        if ($oldMemContribution) {
            $arr['balance'] = $oldMemContribution->balance + $arr['total_contribution'];
        }
        $arr['Surname'] = $request->surname;

        MemberContribution::create($arr);
        return redirect()->route('contribution.index')->with('success', 'Successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show($memberContribution)
    {
        // dd($memberContribution);
        $memberContribution = MemberContribution::where('id', $memberContribution)->first();

        return Inertia::render('Contribution/show', [
            'memberContribution' => $memberContribution,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($memberContribution)
    {
        $products = LoanProduct::with(['charges', 'charges.charge', 'charges.charge.option', 'charges.charge.type'])->where('active', 1)->get();
        $checklists = LoanApplicationChecklist::all();
        $purposes = LoanPurpose::get();
        $approvalStages = LoanApplicationApprovalStage::get();
        $customFields = CustomField::where('category', 'loan_application')->where('active', 1)->get()->transform(function ($item) {
            $item->field_value = '';
            return $item;
        });
        $memberContribution = MemberContribution::where('id', $memberContribution)->first();
        return Inertia::render('Contribution/edit', [
            'memberContribution' => $memberContribution,
            'member_id' => request('member_id'),
            'products' => $products,
            'checklists' => $checklists,
            'purposes' => $purposes,
            'approvalStages' => $approvalStages,
            'designations' => MemberDesignation::all(),
            'categories' => MemberCategory::all(),
            'customFields' => $customFields
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $memberContribution)
    {
        $arr = $request->except('member_type');
        $arr['total_contribution'] = $arr['contri_15_per'] + $arr['contri_30_per'];
        $oldMemContribution = MemberContribution::where('member_id', $arr['member_id'])->latest()->first();
        $arr['balance'] = $oldMemContribution->balance + $arr['total_contribution'];
        $arr['Surname'] = $request->surname;

        MemberContribution::where('id', $memberContribution)->update($arr);
        return redirect()->route('contribution.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($memberContribution)
    {
        // dd($memberContribution);
        MemberContribution::where('id', $memberContribution)->delete();
        return redirect()->route('contribution.index')->with('success', 'Successfully Delete');
    }

    public function import(Request $request){
        // dd($request);
        $import = new MemberContributionImport;

        Excel::import($import, request()->file('file'));
        return redirect()->route('contribution.index')->with('success', 'Successfully imported');
        // Check for validation errors
    // if ($import->getErrors()) {
    //     dd($import->getErrors());
    //     // return redirect('/')->withErrors($import->getErrors());
    // }

    }
}
