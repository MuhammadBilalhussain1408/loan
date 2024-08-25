<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:payment_types.index'])->only(['index', 'show']);
        $this->middleware(['permission:payment_types.create'])->only(['create', 'store']);
        $this->middleware(['permission:payment_types.update'])->only(['edit', 'update']);
        $this->middleware(['permission:payment_types.destroy'])->only(['destroy']);
    }

    public function index()
    {
        $paymentTypes = PaymentType::filter(\request()->only('search'))
            ->paginate(20);
        return Inertia::render('PaymentTypes/Index', [
            'filters' => \request()->all('search'),
            'paymentTypes' => $paymentTypes,
        ]);
    }

    public function create()
    {

        return Inertia::render('PaymentTypes/Create', [
            'chartOfAccounts' => ChartOfAccount::where('active', 1)->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $paymentType = new PaymentType();
        $paymentType->name = $request->name;
        $paymentType->chart_of_account_debit_id = $request->chart_of_account_debit_id;
        $paymentType->chart_of_account_credit_id = $request->chart_of_account_credit_id;
        $paymentType->position = $request->position;
        $paymentType->is_cash = $request->is_cash ? 1 : 0;
        $paymentType->active = $request->active ? 1 : 0;
        $paymentType->report_color = $request->report_color;
        $paymentType->description = $request->description;
        $paymentType->save();
        activity()
            ->performedOn($paymentType)
            ->log('Create PaymentType');
        return redirect()->route('payment_types.index')->with('success', 'PaymentType created successfully.');
    }

    public function show(PaymentType $paymentType)
    {

        return Inertia::render('PaymentTypes/Show', [
            'paymentType' => $paymentType
        ]);
    }

    public function edit(PaymentType $paymentType)
    {
        return Inertia::render('PaymentTypes/Edit', [
            'paymentType' => $paymentType,
            'chartOfAccounts' => ChartOfAccount::where('active', 1)->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    public function update(Request $request, PaymentType $paymentType)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $paymentType->name = $request->name;
        $paymentType->chart_of_account_debit_id = $request->chart_of_account_debit_id;
        $paymentType->chart_of_account_credit_id = $request->chart_of_account_credit_id;
        $paymentType->position = $request->position;
        $paymentType->is_cash = $request->is_cash ? 1 : 0;
        $paymentType->active = $request->active ? 1 : 0;
        $paymentType->report_color = $request->report_color;
        $paymentType->options = $request->options;
        $paymentType->description = $request->description;
        $paymentType->save();
        activity()
            ->performedOn($paymentType)
            ->log('Update PaymentType');
        return redirect()->route('payment_types.index')->with('success', 'PaymentType updated successfully.');
    }

    public function destroy(PaymentType $paymentType)
    {

        $paymentType->delete();
        activity()
            ->performedOn($paymentType)
            ->log('Delete PaymentType');
        return redirect()->route('payment_types.index')->with('success', 'PaymentType deleted successfully.');
    }
}
