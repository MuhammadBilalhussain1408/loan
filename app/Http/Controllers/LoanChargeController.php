<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\LoanCharge;
use App\Models\LoanChargeOption;
use App\Models\LoanChargeType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.charges.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.charges.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.charges.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.charges.destroy'])->only(['destroy']);

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
        $results = LoanCharge::filter(\request()->only('search','loan_charge_type_id', 'loan_charge_option_id', 'currency_id'))
            ->with(['currency', 'type', 'option'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('LoanCharges/Index', [
            'filters' => \request()->all('search', 'loan_charge_type_id', 'loan_charge_option_id', 'currency_id'),
            'results' => $results
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('LoanCharges/Create', [
            'types' => LoanChargeType::orderBy('id')->get(),
            'options' => LoanChargeOption::orderBy('id')->get(),
            'currencies' => Currency::where('active', 1)->orderBy('name')->get()
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
            'currency_id' => ['required'],
            'loan_charge_option_id' => ['required'],
            'loan_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'is_penalty' => ['required'],
            'allow_override' => ['required'],
        ]);
        $charge = new LoanCharge();
        $charge->created_by_id = Auth::id();
        $charge->currency_id = $request->currency_id;
        $charge->loan_charge_type_id = $request->loan_charge_type_id;
        $charge->loan_charge_option_id = $request->loan_charge_option_id;
        $charge->name = $request->name;
        $charge->amount = $request->amount;
        $charge->is_penalty = $request->is_penalty;
        $charge->active = $request->active;
        $charge->allow_override = $request->allow_override;
        $charge->save();
        return redirect()->route('loans.charges.index')->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanCharge $charge
     * @return \Inertia\Response
     */
    public function edit(LoanCharge $charge)
    {
        return Inertia::render('LoanCharges/Edit', [
            'charge' => $charge,
            'types' => LoanChargeType::orderBy('id')->get(),
            'options' => LoanChargeOption::orderBy('id')->get(),
            'currencies' => Currency::where('active', 1)->orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanCharge $charge
     * @return RedirectResponse
     */
    public function update(Request $request, LoanCharge $charge)
    {
        $request->validate([
            'currency_id' => ['required'],
            'loan_charge_option_id' => ['required'],
            'loan_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'is_penalty' => ['required'],
            'allow_override' => ['required'],
        ]);
        $charge->currency_id = $request->currency_id;
        $charge->loan_charge_type_id = $request->loan_charge_type_id;
        $charge->loan_charge_option_id = $request->loan_charge_option_id;
        $charge->name = $request->name;
        $charge->amount = $request->amount;
        $charge->is_penalty = $request->is_penalty;
        $charge->active = $request->active;
        $charge->allow_override = $request->allow_override;
        $charge->save();
        return redirect()->route('loans.charges.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanCharge $charge
     * @return RedirectResponse
     */
    public function destroy(LoanCharge $charge)
    {
        $charge->delete();
        return redirect()->route('loans.charges.index')->with('success', 'Successfully deleted');
    }
}
