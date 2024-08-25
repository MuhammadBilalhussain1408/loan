<?php

namespace App\Http\Controllers;

use App\Models\LoanCollateralType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoanCollateralTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.collateral_types.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.collateral_types.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.collateral_types.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.collateral_types.destroy'])->only(['destroy']);

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
        $results = LoanCollateralType::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('CollateralTypes/Index', [
            'filters' => \request()->all('search'),
            'results' => $results
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('CollateralTypes/Create');
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
        $type = new LoanCollateralType();
        $type->name = $request->name;
        $type->save();
        activity()->on($type)
            ->withProperties(['id' => $type->id])
            ->log('Create Loan Collateral Type');
        return redirect()->route('loans.collateral_types.index')->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanCollateralType $type
     * @return \Inertia\Response
     */
    public function edit(LoanCollateralType $type)
    {
        return Inertia::render('CollateralTypes/Edit', [
            'type' => $type
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanCollateralType $type
     * @return RedirectResponse
     */
    public function update(Request $request, LoanCollateralType $type)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $type->name = $request->name;
        $type->save();
        activity()->on($type)
            ->withProperties(['id' => $type->id])
            ->log('Update Loan Collateral Type');
        return redirect()->route('loans.collateral_types.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanCollateralType $type
     * @return RedirectResponse
     */
    public function destroy(LoanCollateralType $type)
    {
        $type->delete();
        activity()->on($type)
            ->withProperties(['id' => $type->id])
            ->log('Delete Loan Collateral Type');
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
