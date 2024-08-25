<?php

namespace App\Http\Controllers;

use App\Models\LoanPurpose;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanPurposeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.purposes.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.purposes.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.purposes.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.purposes.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $results = LoanPurpose::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('LoanPurposes/Index', [
            'filters' => \request()->all('search'),
            'results' => $results,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('LoanPurposes/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $purpose = new LoanPurpose();
        $purpose->name = $request->name;
        $purpose->save();
        activity()->on($purpose)
            ->withProperties(['id' => $purpose->id])
            ->log('Create Loan Purpose');
        return redirect()->route('loans.purposes.index')->with('success','Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanPurpose $purpose
     * @return \Inertia\Response
     */
    public function edit(LoanPurpose $purpose)
    {

        return Inertia::render('LoanPurposes/Edit', [
            'purpose'=>$purpose
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanPurpose $purpose
     * @return RedirectResponse
     */
    public function update(Request $request, LoanPurpose $purpose)
    {
        $request->validate([
            'name' => ['required'],
        ]);

        $purpose->name = $request->name;
        $purpose->save();
        activity()->on($purpose)
            ->withProperties(['id' => $purpose->id])
            ->log('Update Loan Purpose');
        return redirect()->route('loans.purposes.index')->with('success','Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanPurpose $purpose
     * @return RedirectResponse
     */
    public function destroy(LoanPurpose $purpose)
    {
        $purpose->delete();
        activity()->on($purpose)
            ->withProperties(['id' => $purpose->id])
            ->log('Delete Loan Purpose');
        return redirect()->back()->with('success','Successfully deleted');
    }
}
