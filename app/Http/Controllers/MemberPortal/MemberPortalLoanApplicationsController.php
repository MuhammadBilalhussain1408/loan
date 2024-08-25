<?php

namespace App\Http\Controllers\MemberPortal;

use App\Events\LoanApplicationCreated;
use App\Http\Controllers\Controller;
use App\Models\LoanApplicationLinkedCharge;
use App\Models\LoanCharge;
use App\Models\Member;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Fund;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\LoanPurpose;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MemberPortalLoanApplicationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';
        $results = LoanApplication::filter(\request()->only('search', 'status', 'fund_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'))
            ->with(['member', 'branch', 'product'])
            ->where('member_id', Auth::user()->member->id)
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('MemberPortal/LoanApplications/Index', [
            'filters' => \request()->all('search', 'currency_id', 'fund_id', 'status'),
            'results' => $results
        ]);
    }

    public function create()
    {
        $products = LoanProduct::with(['charges', 'charges.charge', 'charges.charge.option', 'charges.charge.type'])->where('active', 1)->get();
        $purposes = LoanPurpose::get();
        $customFields = CustomField::where('category', 'loan')->where('active', 1)->get();
        return Inertia::render('MemberPortal/LoanApplications/Create', [
            'products' => $products,
            'purposes' => $purposes,
            'customFields' => $customFields
        ]);
    }

    public function store(Request $request)
    {
        $product = LoanProduct::find($request->loan_product_id);
        $request->validate([
            'loan_product_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
        ]);
        $member = Member::find(Auth::user()->member->id);
        $application = new LoanApplication();
        $application->member_id = $member->id;
        $application->created_by_id = Auth::id();
        $application->branch_id = $member->branch_id;
        $application->applied_amount = $request->applied_amount;
        $application->description = $request->description;
        $application->currency_id = $product->currency_id;
        $application->loan_product_id = $product->id;
        $application->interest_rate = $product->default_interest_rate;
        $application->interest_rate_type = $product->interest_rate_type;
        $application->grace_on_principal_paid = $product->grace_on_principal_paid;
        $application->grace_on_interest_paid = $product->grace_on_interest_paid;
        $application->grace_on_interest_charged = $product->grace_on_interest_charged;
        $application->interest_methodology = $product->interest_methodology;
        $application->amortization_method = $product->amortization_method;
        $application->auto_disburse = $product->auto_disburse;
        $application->status = 'pending';
        $application->source = 'member';
        $application->save();
        foreach ($product->charges as $key) {
            if (!empty($key->charge)) {
                $linkedCharge = new LoanApplicationLinkedCharge();
                $linkedCharge->loan_application_id = $application->id;
                $linkedCharge->name = $key->charge->name;
                $linkedCharge->loan_charge_id = $key->charge->id;
                $linkedCharge->amount = $key->charge->amount;
                $linkedCharge->loan_charge_type_id = $key->charge->loan_charge_type_id;
                $linkedCharge->loan_charge_option_id = $key->charge->loan_charge_option_id;
                $linkedCharge->is_penalty = $key->charge->is_penalty;
                $linkedCharge->save();
            }
        }
        event(new LoanApplicationCreated($application));
        return redirect()->route('portal.loans.applications.index')->with('success', 'Successfully applied');
    }

    public function destroy(Request $request, LoanApplication $application)
    {
        if ($application->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }

        if ($application->status != 'pending') {
            return redirect()->route('portal.loans.applications.index')->with('error', 'Failed to delete');

        }
        $application->delete();
        return redirect()->route('portal.loans.applications.index')->with('success', 'Successfully deleted');

    }
}
