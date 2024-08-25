<?php

namespace App\Http\Controllers;

use App\Events\MemberCreated;
use App\Events\MemberStatusChanged;
use App\Imports\MembersImport;
use App\Models\Branch;
use App\Models\LoanApplication;
use App\Models\Member;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Fund;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanPurpose;
use App\Models\MemberCategory;
use App\Models\MemberDesignation;
use App\Models\Profession;
use App\Models\Title;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;


class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.index'])->only(['index', 'show', 'get_members']);
        $this->middleware(['permission:members.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.destroy'])->only(['destroy']);
        $this->middleware(['permission:members.users.create'])->only(['store_user', 'create_user']);
        $this->middleware(['permission:.members.users.destroy'])->only(['destroy_user']);
        $this->middleware(['permission:members.activate'])->only(['change_status']);

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
        $search = $request->s;
        $status = $request->status;
        $results = Member::filter(\request()->only('search', 'status', 'gender', 'branch_id'))
            ->with(['title', 'branch', 'profession', 'country', 'loanOfficer'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('members.status', $status);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('Members/Index', [
            'filters' => \request()->all('search', 'status', 'gender', 'branch_id'),
            'results' => $results,
            'branches' => Branch::all(),
            'countries' => Country::all(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Members/Create', [
            'titles' => Title::all(),
            'professions' => Profession::all(),
            'branches' => Branch::all(),
            'countries' => Country::all(),
            'designations' => MemberDesignation::all(),
            'categories' => MemberCategory::all(),
            'customFields' => CustomField::where('category', 'member')->where('active', 1)->get()->transform(function ($item) {
                $item->field_value = '';
                return $item;
            })
        ]);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'email' => ['nullable', 'email', 'max:255'],
            'dob' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);
        $member = new Member();
        $member->created_by_id = Auth::id();
        $member->first_name = $request->first_name;
        $member->middle_name = $request->middle_name;
        $member->last_name = $request->last_name;
        $member->external_id = $request->external_id;
        $member->gender = $request->gender;
        $member->country_id = $request->country_id;
        $member->loan_officer_id = $request->loan_officer_id;
        $member->title_id = $request->title_id;
        $member->member_category_id = $request->member_category_id;
        $member->member_designation_id = $request->member_designation_id;
        $member->branch_id = $request->branch_id;
        $member->graded_tax_number = $request->graded_tax_number;
        $member->profession_id = $request->profession_id;
        $member->identification_number = $request->identification_number;
        $member->number_of_spouses = $request->number_of_spouses;
        $member->number_of_children = $request->number_of_children;
        $member->email = $request->email;
        $member->contact_number = $request->contact_number;
        $member->home_number = $request->home_number;
        $member->status = $request->status;
        $member->address = $request->address;
        $member->english = $request->english;
        $member->eswatini = $request->eswatini;
        $member->other_language = $request->other_language;
        $member->monthly_or_annual_salary = $request->monthly_or_annual_salary;
        $member->date_of_appointment = $request->date_of_appointment;
        $member->term_end_date = $request->term_end_date;
        $member->marital_status = $request->marital_status;
        $member->postal_address = $request->postal_address;
        $member->description = $request->description;
        $member->dob = $request->dob ? $request->dob : null;
        $member->save();
        if ($request->file('photo')) {
            $member->updateProfilePhoto($request->file('photo'));
        }
        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $member->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $member->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        event(new MemberCreated($member));
        return redirect()->route('members.show', $member->id)->with('success', 'Successfully created');
    }

    public function createImport()
    {
        return Inertia::render('Members/Import', [
        ]);
    }

    public function importMembers(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xlx'],
        ]);
        Excel::import(new MembersImport(), $request->file);

        return redirect()->route('members.index')->with('success', 'Successfully imported');

        return response()->json(['message' => 'Successfully created']);
    }

    /**
     * Show the specified resource.
     * @param Member $member
     * @return \Inertia\Response
     */
    public function show(Member $member)
    {
        $member->load(['loanOfficer','category','designation',  'country', 'branch', 'profession', 'title','other_loan']);
        $customFields = CustomField::where('category', 'member')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($member) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'member')->where('parent_id', $member->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $member->custom_fields = $customFields;

        return Inertia::render('Members/Show', [
            'member' => $member
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit(Member $member)
    {

        $customFields = CustomField::where('category', 'member')->where('active', 1)->get();
        $customFields->transform(function ($item) use ($member) {
            $meta = CustomFieldMeta::where('custom_field_id', $item->id)->where('category', 'member')->where('parent_id', $member->id)->first();
            if ($meta) {
                $item->field_value = $meta->value;
            } else {
                $item->field_value = null;
            }
            return $item;
        });
        $member->custom_fields = $customFields;
        return Inertia::render('Members/Edit', [
            'member' => $member,
            'titles' => Title::all(),
            'professions' => Profession::all(),
            'branches' => Branch::all(),
            'countries' => Country::all(),
            'designations' => MemberDesignation::all(),
            'categories' => MemberCategory::all(),
            'customFields' => $customFields
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'email' => ['nullable', 'email', 'max:255'],
            'dob' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);
        $member->first_name = $request->first_name;
        $member->middle_name = $request->middle_name;
        $member->last_name = $request->last_name;
        $member->external_id = $request->external_id;
        $member->gender = $request->gender;
        $member->country_id = $request->country_id;
        $member->loan_officer_id = $request->loan_officer_id;
        $member->title_id = $request->title_id;
        $member->member_category_id = $request->member_category_id;
        $member->member_designation_id = $request->member_designation_id;
        $member->branch_id = $request->branch_id;
        $member->graded_tax_number = $request->graded_tax_number;
        $member->profession_id = $request->profession_id;
        $member->identification_number = $request->identification_number;
        $member->number_of_spouses = $request->number_of_spouses;
        $member->number_of_children = $request->number_of_children;
        $member->email = $request->email;
        $member->contact_number = $request->contact_number;
        $member->home_number = $request->home_number;
        $member->status = $request->status;
        $member->address = $request->address;
        $member->english = $request->english;
        $member->eswatini = $request->eswatini;
        $member->other_language = $request->other_language;
        $member->monthly_or_annual_salary = $request->monthly_or_annual_salary;
        $member->date_of_appointment = $request->date_of_appointment;
        $member->term_end_date = $request->term_end_date;
        $member->marital_status = $request->marital_status;
        $member->postal_address = $request->postal_address;
        $member->description = $request->description;
        $member->dob = $request->dob ? $request->dob : null;
        $member->save();
        if ($request->file('photo')) {
            $member->updateProfilePhoto($request->file('photo'));
        }
        //save custom fields
        foreach ($request->custom_fields as $field) {
            if (is_array($field['field_value'])) {
                $value = json_encode($field['field_value']);
            } else {
                $value = $field['field_value'];
            }
            CustomFieldMeta::updateOrCreate(
                ['category' => $field['category'], 'parent_id' => $member->id, 'custom_field_id' => $field['id']],
                [
                    'category' => $field['category'],
                    'parent_id' => $member->id,
                    'custom_field_id' => $field['id'],
                    'value' => $value
                ]
            )->save();
        }
        return redirect()->route('members.show', $member->id)->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param Member $member
     * @return RedirectResponse
     */
    public function destroy(Member $member)
    {
        $member->beneficiaries()->delete();
        $member->files()->delete();
        $member->loanApplications()->delete();
        $member->loans()->delete();
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Successfully deleted');
    }

    public function change_status(Request $request, Member $member)
    {
        $request->validate([
            'status' => ['required'],
            //'date' => ['required', 'date'],
        ]);
        $member->status = $request->status;
        $member->save();
        if ($member->wasChanged('status')) {
            event(new MemberStatusChanged($member));
        }

        return redirect()->back()->with('success', 'Successfully updated');
    }

    public function search(Request $request)
    {
        $search = $request->s;
        $id = $request->id;
        $withSavings = $request->with_savings;
        $withLoans = $request->with_loans;
        $query = Member::with(['country', 'branch'])->where(function ($query) use ($search) {
            $query->where('first_name', 'like', "%$search%");
            $query->orWhere('last_name', 'like', "%$search%");
            $query->orWhere('middle_name', 'like', "%$search%");
            $query->orWhere('id', 'like', "%$search%");
            $query->orWhere('account_number', 'like', "%$search%");
            $query->orWhere('identification_number', 'like', "%$search%");
            $query->orWhere('graded_tax_number', 'like', "%$search%");
            $query->orWhere('external_id', 'like', "%$search%");
        })->when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        });
        if (!empty($withSavings)) {
            $query->with('savings');
        }
        if ($withLoans) {
            $query->with('loans');
        }
        $data = $query->get();
        return response()->json($data);
    }

    public function loans(Request $request, Member $member)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';

        $results = Loan::filter(\request()->only('search', 'status', 'member_id', 'fund_id', 'loan_product_id', 'currency_id', 'branch_id', 'loanOfficer', 'loan_purpose_id', 'loan_provisioning_id'))
            ->with(['member', 'branch', 'loanOfficer', 'product', 'currency'])
            ->where('member_id', $member->id)
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        $products = LoanProduct::where('active', 1)->get();
        $currencies = Currency::where('active', 1)->get();
        $purposes = LoanPurpose::get();
        return Inertia::render('Members/Loans/Index', [
            'filters' => \request()->all('search', 'status', 'member_id', 'fund_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'),
            'member' => $member,
            'results' => $results,
            'products' => $products,
            'purposes' => $purposes,
            'currencies' => $currencies,
        ]);
    }

    public function loanApplications(Request $request, Member $member)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';

        $results = LoanApplication::filter(\request()->only('search', 'status', 'member_id', 'fund_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'))
            ->with(['member', 'branch', 'product', 'currentStage', 'nextStage', 'currentStage.stage', 'nextStage.stage'])
            ->where('member_id', $member->id)
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        $products = LoanProduct::where('active', 1)->get();
        $currencies = Currency::where('active', 1)->get();
        $purposes = LoanPurpose::get();
        return Inertia::render('Members/LoanApplications/Index', [
            'filters' => \request()->all('search', 'status', 'member_id', 'fund_id', 'loan_product_id', 'currency_id', 'branch_id', 'loan_officer_id', 'loan_purpose_id', 'loan_provisioning_id'),
            'member' => $member,
            'results' => $results,
            'products' => $products,
            'purposes' => $purposes,
            'currencies' => $currencies,
        ]);
    }

}
