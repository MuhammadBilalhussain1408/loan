<?php

namespace App\Http\Controllers;

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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StopLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';

        $results = StopLoan::
            paginate($perPage)
            ->appends($request->input());
        // $products = LoanProduct::where('active', 1)->get();
        $currencies = Currency::where('active', 1)->get();
        $approvalStages = LoanApplicationApprovalStage::get();
        return Inertia::render('StopLoan/index', [
            'filters' => \request()->all('search', 'status', 'member_id', 'loan_product_id', 'currency_id', 'branch_id', 'current_stage_id', 'next_stage_id'),
            'results' => $results,
            'currencies' => $currencies,
            'approvalStages' => $approvalStages,
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
        return Inertia::render('StopLoan/create', [
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
        // dd($request);

        $stopLoan = StopLoan::create($request->except(['member_type']));
        return redirect()->route('loans.stop_loan.index')->with('success', 'Successfully created');

    }

    /**
     * Display the specified resource.
     */
    public function show($stopLoan)
    {
        // dd($stopLoan);
        $stopLoan = StopLoan::where('id',$stopLoan)->first();

        return Inertia::render('StopLoan/show', [
            'stopLoan' => $stopLoan,
            'assetUrl' => asset('/')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($stopLoan)
    {
        $products = LoanProduct::with(['charges', 'charges.charge', 'charges.charge.option', 'charges.charge.type'])->where('active', 1)->get();
        $checklists = LoanApplicationChecklist::all();
        $purposes = LoanPurpose::get();
        $approvalStages = LoanApplicationApprovalStage::get();
        $customFields = CustomField::where('category', 'loan_application')->where('active', 1)->get()->transform(function ($item) {
            $item->field_value = '';
            return $item;
        });
        $stopLoan = StopLoan::where('id',$stopLoan)->first();
        return Inertia::render('StopLoan/edit', [
            'stop_loan' => $stopLoan,
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
    public function update(Request $request, $stopLoan)
    {
        StopLoan::where('id',$stopLoan)->update($request->except(['member_type']));
        return redirect()->route('loans.stop_loan.index')->with('success', 'Successfully Updated');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($stopLoan)
    {
        // dd($stopLoan);
        StopLoan::where('id',$stopLoan)->delete();
        return redirect()->route('loans.stop_loan.index')->with('success', 'Successfully Delete');
    }
}
