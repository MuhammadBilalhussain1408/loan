<?php

namespace App\Http\Controllers;

use App\Models\LoanProvisioning;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanProvisioningController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.loan_provisioning.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.loan_provisioning.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.loan_provisioning.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.loan_provisioning.destroy'])->only(['destroy']);

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
        $results = LoanProvisioning::filter(\request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());

        return Inertia::render('LoanProvisioning/Index', [
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
        return Inertia::render('LoanProvisioning/Create');
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
            'percentage' => ['required'],
            'lower_limit' => ['required'],
        ]);
        $provisioning = new LoanProvisioning();
        $provisioning->name = $request->name;
        $provisioning->lower_limit = $request->lower_limit;
        $provisioning->upper_limit = $request->upper_limit;
        $provisioning->percentage = $request->percentage;
        $provisioning->description = $request->description;
        $provisioning->save();
        return redirect()->route('loans.provisioning.index')->with('success', 'Successfully saved');

    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanProvisioning $provisioning
     * @return \Inertia\Response
     */
    public function edit(LoanProvisioning $provisioning)
    {

        return Inertia::render('LoanProvisioning/Edit', [
            'provisioning' => $provisioning
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanProvisioning $provisioning
     * @return RedirectResponse
     */
    public function update(Request $request, LoanProvisioning $provisioning)
    {

        $request->validate([
            'name' => ['required'],
            'percentage' => ['required'],
            'lower_limit' => ['required'],
        ]);
        $provisioning->name = $request->name;
        $provisioning->lower_limit = $request->lower_limit;
        $provisioning->upper_limit = $request->upper_limit;
        $provisioning->percentage = $request->percentage;
        $provisioning->description = $request->description;
        $provisioning->save();
        return redirect()->route('loans.provisioning.index')->with('success', 'Successfully saved');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanProvisioning $provisioning
     * @return RedirectResponse
     */
    public function destroy(LoanProvisioning $provisioning)
    {
        $provisioning->delete();
        return redirect()->route('loans.provisioning.index')->with('success', 'Successfully deleted');
    }
}
