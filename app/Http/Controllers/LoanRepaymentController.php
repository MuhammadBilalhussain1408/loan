<?php

namespace App\Http\Controllers;


use App\Imports\RepaymentsImport;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;


class LoanRepaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.transactions.index'])->only(['index', 'get_loans', 'show', 'show_application', 'get_applications']);
        $this->middleware(['permission:loans.create'])->only(['create', 'create_member_loan', 'store_member_loan', 'store']);
        $this->middleware(['permission:loans.edit'])->only(['edit', 'edit_member_loan', 'update', 'update_member_loan', 'change_loan_officer']);
        $this->middleware(['permission:loans.destroy'])->only(['destroy']);
        $this->middleware(['permission:loans.approve_loan'])->only(['approve_loan', 'undo_approval', 'reject_loan', 'undo_rejection', 'approve_application', 'store_approve_application']);
        $this->middleware(['permission:loans.disburse_loan'])->only(['disburse_loan', 'undo_disbursement']);
        $this->middleware(['permission:loans.withdraw_loan'])->only(['withdraw_loan', 'undo_withdrawn']);
        $this->middleware(['permission:loans.write_off_loan'])->only(['write_off_loan', 'undo_write_off']);
        $this->middleware(['permission:loans.reschedule_loan'])->only(['reschedule_loan']);
        $this->middleware(['permission:loans.close_loan'])->only(['close_loan', 'undo_close']);
        $this->middleware(['permission:loans.calculator'])->only(['calculator']);
        $this->middleware(['permission:loans.transactions.create'])->only(['create_repayment', 'store_repayment', 'create_loan_linked_charge', 'store_loan_linked_charge']);
        $this->middleware(['permission:loans.transactions.edit'])->only(['edit_repayment', 'reverse_repayment', 'update_repayment', 'waive_interest', 'waive_charge']);
    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';
        $results = LoanTransaction::filter(\request()->only('search', 'payment_type_id', 'member_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'))
            ->with(['createdBy', 'type', 'createdBy', 'paymentDetail', 'paymentDetail.payment_type', 'loan', 'loan.member'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->where('loan_transaction_type_id', LoanTransactionType::where('name', 'Repayment')->first()->id)
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('LoanRepayments/Index', [
            'filters' => \request()->all('search', 'payment_type_id', 'member_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'),
            'results' => $results,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function createBulkRepayment()
    {
        return Inertia::render('LoanRepayments/CreateBulkRepayment', [
            'sampleUrl' => asset('bulk_repayments.xlsx')
        ]);
    }

    public function add()
    {
        return Inertia::render('LoanRepayments/create', []);
    }
    public function storeRepayment(Request $request) {
        dd($request);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeBulkRepayment(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,csv'],
        ]);
        Excel::import(new RepaymentsImport(Auth::user()), $request->file);
        activity()->log('Bulk import repayments');
        //fire loan status changed event
        return redirect()->route('loans.repayments.index')->with('success', 'Import now running in the background');
    }
}
