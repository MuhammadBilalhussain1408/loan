<?php

namespace App\Http\Controllers;


use App\Events\LoanApplicationApprovalStageAssigned;
use App\Events\LoanApplicationApprovalStageStatusChanged;
use App\Events\LoanApplicationCreated;
use App\Events\LoanApplicationCurrentApprovalStageChanged;
use App\Events\LoanApplicationStatusChanged;
use App\Events\LoanCreated;
use App\Events\LoanStatusChanged;
use App\Events\TransactionUpdated;
use App\Models\LoanApplicationApprovalStage;
use App\Models\LoanApplicationChecklist;
use App\Models\LoanApplicationHistory;
use App\Models\LoanApplicationLinkedApprovalStage;
use App\Models\LoanApplicationLinkedCharge;
use App\Models\LoanApplicationLinkedChecklistItem;
use App\Models\Member;
use App\Models\Currency;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Fund;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanCharge;
use App\Models\LoanHistory;
use App\Models\LoanLinkedCharge;
use App\Models\LoanOfficerHistory;
use App\Models\LoanProduct;
use App\Models\LoanPurpose;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\MemberCategory;
use App\Models\MemberDesignation;
use App\Models\PaymentType;
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


class LoanApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.applications.index'])->only(['index', 'get_loans', 'show', 'show_application', 'get_applications']);
        $this->middleware(['permission:loans.applications.create'])->only(['create', 'approve', 'store_member_loan', 'store']);
        $this->middleware(['permission:loans.applications.update'])->only(['edit', 'changeStatus', 'update', 'update_member_loan', 'change_loan_officer']);
        $this->middleware(['permission:loans.applications.destroy'])->only(['destroy']);
        $this->middleware(['permission:loans.applications.update_checklist_items'])->only(['changeChecklistItemStatus']);
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

        $results = LoanApplication::filter(\request()->only('search', 'status', 'member_id', 'loan_product_id', 'currency_id', 'branch_id', 'current_stage_id', 'next_stage_id'))
            ->with(['member', 'loan', 'branch', 'product', 'currentStage', 'nextStage', 'currentStage.stage', 'nextStage.stage'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        $products = LoanProduct::where('active', 1)->get();
        $currencies = Currency::where('active', 1)->get();
        $approvalStages = LoanApplicationApprovalStage::get();
        return Inertia::render('LoanApplications/Index', [
            'filters' => \request()->all('search', 'status', 'member_id', 'loan_product_id', 'currency_id', 'branch_id', 'current_stage_id', 'next_stage_id'),
            'results' => $results,
            'products' => $products,
            'currencies' => $currencies,
            'approvalStages' => $approvalStages,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
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
        return Inertia::render('LoanApplications/Create', [
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $product = LoanProduct::find($request->loan_product_id);
        $request->validate([
            'member_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric', 'lte:' . $product->maximum_loan_term, 'gte:' . $product->minimum_loan_term],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
        ]);
        $member = Member::find($request->member_id);
        $application = new LoanApplication();
        $application->currency_id = $product->currency_id;
        $application->loan_product_id = $product->id;
        $application->member_id = $member->id;
        $application->member_category_id = $request->member_category_id;
        $application->member_designation_id = $request->member_designation_id;
        $application->branch_id = $member->branch_id;
        $application->loan_application_checklist_id = $request->loan_application_checklist_id;
        $application->loan_purpose_id = $request->loan_purpose_id;
        $application->loan_officer_id = $request->loan_officer_id;
        $application->created_by_id = Auth::id();

        $application->applied_amount = $request->applied_amount;

        $application->loan_term = $request->loan_term;
        $application->repayment_frequency = $request->repayment_frequency;
        $application->repayment_frequency_type = $request->repayment_frequency_type;
        $application->interest_rate = $product->disallow_interest_rate_adjustment ? $product->default_interest_rate : $request->interest_rate;
        $application->interest_rate_type = $product->interest_rate_type;
        $application->grace_on_principal_paid = $product->grace_on_principal_paid;
        $application->grace_on_interest_paid = $product->grace_on_interest_paid;
        $application->grace_on_interest_charged = $product->grace_on_interest_charged;
        $application->interest_methodology = $product->interest_methodology;
        $application->amortization_method = $product->amortization_method;
        $application->auto_disburse = $product->auto_disburse;
        if ($request->admin_charges) {
            $application->admin_charges = ($request->applied_amount * $request->admin_charges) / 100;
        }
        $application->status = 'pending';
        $application->save();
        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $application->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $application->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        //save charges
        // if (!empty($request->selected_charges)) {
        //     foreach ($request->selected_charges as $key) {
        //         $charge = LoanCharge::find($key['loan_charge_id']);
        //         $linkedCharge = new LoanApplicationLinkedCharge();
        //         $linkedCharge->loan_application_id = $application->id;
        //         $linkedCharge->name = $charge->name;
        //         $linkedCharge->loan_charge_id = $charge->id;
        //         if ($charge->allow_override == 1) {
        //             $linkedCharge->amount = $key['amount'];
        //         } else {
        //             $linkedCharge->amount = $charge->amount;
        //         }
        //         $linkedCharge->loan_charge_type_id = $charge->loan_charge_type_id;
        //         $linkedCharge->loan_charge_option_id = $charge->loan_charge_option_id;
        //         $linkedCharge->is_penalty = $charge->is_penalty;
        //         $linkedCharge->save();
        //     }
        // }

        if ($request->admin_charges) {

            //         $charge = LoanCharge::first();
            $linkedCharge = new LoanApplicationLinkedCharge();
            //         $linkedCharge->loan_application_id = $application->id;
            //         $linkedCharge->name = $charge->name;
            //         $linkedCharge->loan_charge_id = $charge->id;
            // if ($charge->allow_override == 1) {
            $linkedCharge->amount = ($request->applied_amount * $request->admin_charges) / 100;

            //dd($linkedCharge->amount);
            // } else {
            //$linkedCharge->amount = $charge->amount;
            //}
            // $linkedCharge->loan_charge_type_id = $charge->loan_charge_type_id;
            // $linkedCharge->loan_charge_option_id = $charge->loan_charge_option_id;
            // $linkedCharge->is_penalty = $charge->is_penalty;
            $linkedCharge->save();
        }


        //fire loan application created event
        event(new LoanApplicationCreated($application));
        return redirect()->route('loans.applications.show', $application->id)->with('success', 'Successfully created');
    }

    public function edit(LoanApplication $application)
    {
        $application->load(['member', 'loan', 'product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);
        if (empty($application->product)) {
            return redirect()->back()->with('error', 'Linked product not found');
        }
        if (empty($application->member)) {
            return redirect()->back()->with('error', 'Linked member not found');
        }
        $checklists = LoanApplicationChecklist::all();
        $purposes = LoanPurpose::get();
        $customFields = CustomField::where('category', 'loan_application')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($application) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'loan_application')->where('parent_id', $application->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $application->custom_fields = $customFields;
        $application->selected_charges = $application->charges->map(function ($item) {
            return [
                'id' => $item->id,
                'loan_charge_id' => $item->loan_charge_id,
                'name' => $item->charge->name,
                'type' => $item->charge->type->name,
                'option' => $item->charge->option->name,
                'amount' => $item->amount,
            ];
        })->toArray();
        return Inertia::render('LoanApplications/Edit', [
            'application' => $application,
            'checklists' => $checklists,
            'purposes' => $purposes,
            'designations' => MemberDesignation::all(),
            'categories' => MemberCategory::all(),
            'customFields' => $customFields
        ]);
    }

    public function update(Request $request, LoanApplication $application)
    {
        if ($application->status === 'approved') {
            return redirect()->back()->with('error', 'You cannot edit an approved application');
        }
        $request->validate([
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric', 'lte:' . $application->product->maximum_loan_term, 'gte:' . $application->product->minimum_loan_term],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
        ]);
        $application->member_category_id = $request->member_category_id;
        $application->member_designation_id = $request->member_designation_id;
        $application->loan_purpose_id = $request->loan_purpose_id;
        $application->loan_officer_id = $request->loan_officer_id;
        $application->loan_application_checklist_id = $request->loan_application_checklist_id;
        $application->applied_amount = $request->applied_amount;
        $application->loan_term = $request->loan_term;
        $application->repayment_frequency = $request->repayment_frequency;
        $application->repayment_frequency_type = $request->repayment_frequency_type;
        if (!$application->product->disallow_interest_rate_adjustment) {
            $application->interest_rate = $request->interest_rate;
        }
        $application->save();
        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $application->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $application->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        //save charges
        $application->charges()->delete();
        if (!empty($request->selected_charges)) {
            foreach ($request->selected_charges as $key) {
                $charge = LoanCharge::find($key['loan_charge_id']);
                $linkedCharge = new LoanApplicationLinkedCharge();
                $linkedCharge->loan_application_id = $application->id;
                $linkedCharge->name = $charge->name;
                $linkedCharge->loan_charge_id = $charge->id;
                if ($charge->allow_override == 1) {
                    $linkedCharge->amount = $key['amount'];
                } else {
                    $linkedCharge->amount = $charge->amount;
                }
                $linkedCharge->loan_charge_type_id = $charge->loan_charge_type_id;
                $linkedCharge->loan_charge_option_id = $charge->loan_charge_option_id;
                $linkedCharge->is_penalty = $charge->is_penalty;
                $linkedCharge->save();
            }
        }
        //save checklist items
        if ($application->checklist) {
            $application->checklist->items->each(function ($item) use ($application) {
                LoanApplicationLinkedChecklistItem::updateOrCreate([
                    'loan_application_id' => $application->id,
                    'loan_application_checklist_item_id' => $item->id,
                ], [
                    'name' => $item->name,
                ]);
            });
            $application->checklistItems()->whereNotIn('loan_application_checklist_item_id', $application->checklist->items->pluck('id')->toArray())->delete();
        } else {
            $application->checklistItems()->delete();
        }
        $applicationHistory = new LoanApplicationHistory();
        $applicationHistory->loan_application_id = $application->id;
        $applicationHistory->created_by_id = Auth::id();
        $applicationHistory->user = Auth::user()->name;
        $applicationHistory->action = 'Loan Application Edited';
        $applicationHistory->save();
        return redirect()->route('loans.applications.show', $application->id)->with('success', 'Successfully updated');
    }

    public function show(LoanApplication $application)
    {
        $application->load(['member', 'loan', 'category', 'designation', 'currency', 'branch', 'product', 'purpose', 'charges', 'charges.charge', 'checklist', 'checklist.items', 'currentStage', 'currentStage.stage', 'nextStage', 'nextStage.stage', 'stages', 'stages.stage', 'stages.assignedTo', 'checklistItems', 'checklistItems.item']);
        if (empty($application->product)) {
            return redirect()->back()->with('error', 'Linked product not found');
        }
        if (empty($application->member)) {
            return redirect()->back()->with('error', 'Linked member not found');
        }
        $customFields = CustomField::where('category', 'loan_application')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($application) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'loan_application')->where('parent_id', $application->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $application->custom_fields = $customFields;
        return Inertia::render('LoanApplications/Show', [
            'application' => $application,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    public function history(Request $request, LoanApplication $application)
    {
        $application->load(['member', 'loan', 'category', 'designation', 'currency', 'branch', 'product', 'purpose', 'charges', 'charges.charge', 'checklist', 'checklist.items', 'currentStage', 'currentStage.stage', 'nextStage', 'nextStage.stage', 'stages', 'stages.stage', 'stages.assignedTo', 'checklistItems', 'checklistItems.item']);
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';
        $results = LoanApplicationHistory::filter(\request()->only('search'))
            ->where('loan_application_id', $application->id)
            ->with(['createdBy'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());

        return Inertia::render('LoanApplications/Histories/Index', [
            'filters' => \request()->all('search'),
            'results' => $results,
            'application' => $application,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    public function charges(Request $request, LoanApplication $application)
    {
        $application->load(['member', 'loan', 'category', 'designation', 'currency', 'branch', 'product', 'purpose', 'charges', 'charges.charge', 'checklist', 'checklist.items', 'currentStage', 'currentStage.stage', 'nextStage', 'nextStage.stage', 'stages', 'stages.stage', 'stages.assignedTo', 'checklistItems', 'checklistItems.item']);


        return Inertia::render('LoanApplications/Charges/Index', [
            'filters' => \request()->all('search'),
            'application' => $application,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    public function schedule(Request $request, LoanApplication $application)
    {
        $application->load(['member', 'loan', 'category', 'designation', 'currency', 'branch', 'product', 'purpose', 'charges', 'charges.charge', 'checklist', 'checklist.items', 'currentStage', 'currentStage.stage', 'nextStage', 'nextStage.stage', 'stages', 'stages.stage', 'stages.assignedTo', 'checklistItems', 'checklistItems.item']);
        $schedule = generate_loan_application_schedule($application);
        return Inertia::render('LoanApplications/Schedules/Index', [
            'filters' => \request()->all('search'),
            'application' => $application,
            'loan_details' => $schedule['loan_details'],
            'schedules' => $schedule['schedules'],
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    public function changeStatus(Request $request, LoanApplication $application)
    {
        $request->validate([
            'status' => ['required']
        ]);
        $oldStatus = $application->status;
        $application->status = $request->status;
        $application->save();
        //fire loan application status changed event
        event(new LoanApplicationStatusChanged($application, $oldStatus));
        return redirect()->route('loans.applications.show', $application->id)->with('success', 'Successfully saved');
    }

    public function assignApprovalStage(Request $request, LoanApplication $application)
    {
        $request->validate([
            'assigned_to_id' => ['required'],
            'id' => ['required'],
        ]);
        $linkedStage = LoanApplicationLinkedApprovalStage::find($request->id);
        $linkedStage->assigned_to_id = $request->assigned_to_id;
        $linkedStage->save();
        $applicationHistory = new LoanApplicationHistory();
        $applicationHistory->loan_application_id = $application->id;
        $applicationHistory->created_by_id = Auth::id();
        $applicationHistory->user = Auth::user()->name;
        $applicationHistory->action = 'Loan Application stage(' . $linkedStage->name . ') assigned to ' . $linkedStage->assignedTo->name;
        $applicationHistory->save();
        //fire loan application approval stage assigned event
        if ($linkedStage->wasChanged('assigned_to_id')) {
            event(new LoanApplicationApprovalStageAssigned($linkedStage));
        }
        return response()->json(['message' => 'Successfully saved']);
    }

    public function changeApprovalStageStatus(Request $request, LoanApplication $application)
    {
        $request->validate([
            'status' => ['required'],
            'id' => ['required'],
        ]);

        $linkedStage = LoanApplicationLinkedApprovalStage::find($request->id);
        $oldStatus = $linkedStage->status;
        $linkedStage->status = $request->status;
        $linkedStage->description = $request->description;
        $linkedStage->save();
        //fire loan application approval stage status changed event
        if ($linkedStage->wasChanged('status')) {
            event(new LoanApplicationApprovalStageStatusChanged($linkedStage));
            $applicationHistory = new LoanApplicationHistory();
            $applicationHistory->loan_application_id = $application->id;
            $applicationHistory->created_by_id = Auth::id();
            $applicationHistory->user = Auth::user()->name;
            $applicationHistory->action = 'Loan Application stage(' . $linkedStage->name . ') status changed from ' . $oldStatus . ' to ' . $linkedStage->status . ' by ' . Auth::user()->name;
            $applicationHistory->save();
        }
        return response()->json(['message' => 'Successfully saved']);
    }

    public function changeChecklistItemStatus(Request $request, LoanApplication $application)
    {
        $request->validate([
            'status' => ['required'],
            'id' => ['required'],
        ]);
        $linkedItem = LoanApplicationLinkedChecklistItem::find($request->id);
        $linkedItem->completed = $request->status;
        if ($request->status == 1) {
            $linkedItem->completed_at = Carbon::now();
            $linkedItem->completed_by_id = Auth::id();
            $message = "completed";
        } else {
            $linkedItem->completed_at = null;
            $message = "marked as incomplete";
        }
        $linkedItem->save();
        $applicationHistory = new LoanApplicationHistory();
        $applicationHistory->loan_application_id = $application->id;
        $applicationHistory->created_by_id = Auth::id();
        $applicationHistory->user = Auth::user()->name;
        $applicationHistory->action = 'Loan checklist item(' . $linkedItem->name . ') ' . $message . ' by ' . Auth::user()->name;
        $applicationHistory->save();
        return response()->json(['message' => 'Successfully saved']);
    }

    public function acknowledgeApprovalStage(Request $request, LoanApplication $application)
    {
        $request->validate([
            'id' => ['required'],
        ]);
        $linkedStage = LoanApplicationLinkedApprovalStage::find($request->id);
        $linkedStage->acknowledged = 1;
        $linkedStage->acknowledged_at = Carbon::now();
        $linkedStage->save();
        $applicationHistory = new LoanApplicationHistory();
        $applicationHistory->loan_application_id = $application->id;
        $applicationHistory->created_by_id = Auth::id();
        $applicationHistory->user = Auth::user()->name;
        $applicationHistory->action = 'Loan approval stage(' . $linkedStage->name . ') acknowleded  by ' . Auth::user()->name;
        $applicationHistory->save();
        return response()->json(['message' => 'Successfully saved']);
    }


    /**
     * Remove the specified resource from storage.
     * @param LoanApplication $application
     * @return RedirectResponse
     */
    public function destroy(LoanApplication $application)
    {
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Application not pending');
        }
        $application->charges()->delete();
        $application->notes()->delete();
        $application->files()->delete();
        $application->stages()->delete();
        $application->delete();
        return redirect()->route('loans.applications.index', $application->id)->with('success', 'Successfully deleted');
    }


    public function approve(LoanApplication $application)
    {
        $application->load(['member', 'category', 'designation', 'product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);
        $funds = Fund::all();
        $purposes = LoanPurpose::get();
        $customFields = CustomField::where('category', 'loan')->where('active', 1)->get();
        return Inertia::render('LoanApplications/Approve', [
            'application' => $application,
            'funds' => $funds,
            'purposes' => $purposes,
            'customFields' => $customFields
        ]);
    }


    public function disburse(Request $request, LoanApplication $application)
    {
        $product = $application->product;
        $request->validate([
            'approved_amount' => ['required', 'numeric', 'lte:' . $product->maximum_principal, 'gte:' . $product->minimum_principal],
            'first_payment_date' => ['required', 'date'],
            'disbursed_on_date' => ['required', 'date'],
        ]);

        $application->status = 'disbursed';
        $application->loan_officer_id =  Auth::id();
        $application->approved_amount = $request->approved_amount;
        $application->first_payment_date = $request->first_payment_date;
        $application->disbursed_on_date = $request->disbursed_on_date;
        $application->disbursement_notes = $request->description;
        $application->payment_type_id = $request->payment_type_id;
        $application->save();
        $applicationHistory = new LoanApplicationHistory();
        $applicationHistory->loan_application_id = $application->id;
        $applicationHistory->created_by_id = Auth::id();
        $applicationHistory->user = Auth::user()->name;
        $applicationHistory->action = 'Loan Application disbursed by ' . Auth::user()->name;
        $applicationHistory->save();
        event(new LoanApplicationStatusChanged($application, 'approved'));

        return redirect()->back()->with('success', 'Successfully disbursed application. Loan is now processing in the background.');
    }
}
