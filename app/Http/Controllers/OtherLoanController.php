<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberRelationship;
use App\Models\OtherLoan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OtherLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($member)
    {
        // dd($member);
        $otherLoans = OtherLoan::
       // filter(\request()->only('search'))
            // ->with(['createdBy', 'memberRelationship'])
            where('member_id', $member)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            $member= Member::where('id',$member)->first();
        return Inertia::render('Members/OtherLoans/index', [
            'member' => $member,
            'otherLoans' => $otherLoans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $member= Member::where('id',$id)->first();
        return Inertia::render('Members/OtherLoans/create', [
            'member' => $member,
            'memberRelationships' => MemberRelationship::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $arr = $request->all();
        $arr['member_id'] = $id;
        OtherLoan::create($arr);

        return redirect()->route('members.other_loan.index', $id)->with('success', 'Other Loan created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(OtherLoan $otherLoan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OtherLoan $otherLoan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OtherLoan $otherLoan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OtherLoan $otherLoan)
    {
        //
    }
}
