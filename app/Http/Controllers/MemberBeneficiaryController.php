<?php

namespace App\Http\Controllers;


use App\Models\Member;
use App\Models\MemberBeneficiary;
use App\Models\MemberRelationship;
use App\Models\Country;
use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MemberBeneficiaryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.beneficiaries.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.beneficiaries.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.beneficiaries.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.beneficiaries.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Member $member)
    {
        $beneficiaries = MemberBeneficiary::filter(\request()->only('search'))
            ->with(['createdBy', 'memberRelationship'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Members/Beneficiaries/Index', [
            'member' => $member,
            'beneficiaries' => $beneficiaries,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Member $member)
    {

        return Inertia::render('Members/Beneficiaries/Create', [
            'member' => $member,
            'memberRelationships' => MemberRelationship::get(),
            'countries' => Country::get(),
            'titles' => Title::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, Member $member)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'member_relationship_id' => ['required'],
            'gender' => ['required'],

        ]);
        $beneficiary = new MemberBeneficiary();
        $beneficiary->created_by_id = Auth::id();
        $beneficiary->member_id = $member->id;
        $beneficiary->country_id = $request->country_id;
        $beneficiary->title_id = $request->title_id;
        $beneficiary->shares = $request->shares;
        $beneficiary->member_relationship_id = $request->member_relationship_id;
        $beneficiary->first_name = $request->first_name;
        $beneficiary->middle_name = $request->middle_name;
        $beneficiary->last_name = $request->last_name;
        $beneficiary->identification_number = $request->identification_number;
        $beneficiary->contact_number = $request->contact_number;
        $beneficiary->home_number = $request->home_number;
        $beneficiary->address = $request->address;
        $beneficiary->postal_address = $request->postal_address;
        $beneficiary->description = $request->description;
        $beneficiary->dob = $request->dob;
        $beneficiary->email = $request->email;
        $beneficiary->gender = $request->gender;
        $beneficiary->save();

        return redirect()->route('members.beneficiaries.index', $member->id)->with('success', 'Member  beneficiary created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(MemberBeneficiary $beneficiary)
    {
        $member = $beneficiary->member;
        $beneficiary = MemberBeneficiary::with(['createdBy', 'memberRelationship'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Members/Beneficiaries/Show', [
            'member' => $member,
            'beneficiary' => $beneficiary,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MemberBeneficiary $beneficiary
     * @return \Inertia\Response
     */
    public function edit(MemberBeneficiary $beneficiary)
    {
        $member = $beneficiary->member;

        return Inertia::render('Members/Beneficiaries/Edit', [
            'member' => $member,
            'beneficiary' => $beneficiary,
            'memberRelationships' => MemberRelationship::get(),
            'countries' => Country::get(),
            'titles' => Title::get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param MemberBeneficiary $beneficiary
     * @return RedirectResponse
     */
    public function update(Request $request, MemberBeneficiary $beneficiary)
    {

        $member = $beneficiary->member;
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'member_relationship_id' => ['required'],
            'gender' => ['required'],
        ]);
        $beneficiary->country_id = $request->country_id;
        $beneficiary->title_id = $request->title_id;
        $beneficiary->shares = $request->shares;
        $beneficiary->member_relationship_id = $request->member_relationship_id;
        $beneficiary->first_name = $request->first_name;
        $beneficiary->middle_name = $request->middle_name;
        $beneficiary->last_name = $request->last_name;
        $beneficiary->identification_number = $request->identification_number;
        $beneficiary->contact_number = $request->contact_number;
        $beneficiary->home_number = $request->home_number;
        $beneficiary->address = $request->address;
        $beneficiary->postal_address = $request->postal_address;
        $beneficiary->description = $request->description;
        $beneficiary->dob = $request->dob;
        $beneficiary->email = $request->email;
        $beneficiary->gender = $request->gender;
        $beneficiary->save();

        return redirect()->route('members.beneficiaries.index', [$member->id])->with('success', 'Member  beneficiary updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(MemberBeneficiary $beneficiary)
    {

        $beneficiary->delete();
        return redirect()->route('members.beneficiaries.index', $beneficiary->member_id)->with('success', 'Member  beneficiary deleted successfully.');

    }
}
