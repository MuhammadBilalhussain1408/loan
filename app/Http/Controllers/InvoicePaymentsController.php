<?php

namespace App\Http\Controllers;

use App\Events\InvoicePaymentCreated;
use App\Models\Branch;
use App\Models\CoPayer;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\PaymentDetail;
use App\Models\PaymentType;
use App\Models\Tariff;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use PDF;

class InvoicePaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:billing.payments.index'])->only(['index', 'show']);
        $this->middleware(['permission:billing.payments.create'])->only(['create', 'store']);
        $this->middleware(['permission:billing.payments.update'])->only(['edit', 'update']);
        $this->middleware(['permission:billing.payments.destroy'])->only(['destroy']);
    }

    public function index()
    {
        $invoicePayments = InvoicePayment::with(['patient', 'coPayer', 'currency', 'paymentType'])
            ->filter(\request()->only('search', 'co_payer_id', 'paid_by', 'doctor_id', 'patient_id', 'date_range', 'currency_id'))
            ->paginate(20);
        return Inertia::render('InvoicePayments/Index', [
            'filters' => \request()->all('search', 'co_payer_id', 'paid_by', 'doctor_id', 'patient_id', 'date_range', 'currency_id'),
            'invoicePayments' => $invoicePayments,
            'coPayers' => CoPayer::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'currencies' => Currency::where('active', 1)->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'paymentTypes' => PaymentType::where('active', 1)->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    public function create(Invoice $invoice)
    {

        return Inertia::render('InvoicePayments/Create', [
            'invoice' => $invoice,
            'paymentTypes' => PaymentType::where('active', 1)->get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'currencies' => Currency::where('active', 1)->get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'coPayers' => CoPayer::get()->transform(function ($coPayer) {
                return [
                    'value' => $coPayer->id,
                    'label' => $coPayer->name,
                ];
            }),
        ]);
    }

    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'paid_by' => ['required'],
            'amount' => ['required'],
            'payment_type_id' => ['required'],
            'co_payer_id' => ['required_if:paid_by,co_payer'],
        ]);
        $paymentDetail = new PaymentDetail();
        $paymentDetail->created_by_id = Auth::id();
        $paymentDetail->payment_type_id = $request->payment_type_id;
        $paymentDetail->transaction_type = 'invoice_payment';
        $paymentDetail->cheque_number = $request->cheque_number;
        $paymentDetail->receipt = $request->receipt;
        $paymentDetail->account_number = $request->account_number;
        $paymentDetail->bank_name = $request->bank_name;
        $paymentDetail->routing_code = $request->routing_code;
        $paymentDetail->save();
        $invoicePayment = new InvoicePayment();
        $invoicePayment->created_by_id = Auth::id();
        $invoicePayment->invoice_id = $invoice->id;
        $invoicePayment->payment_detail_id = $paymentDetail->id;
        $invoicePayment->currency_id = $request->currency_id ?: $invoice->currency_id;
        $invoicePayment->xrate =  $invoice->xrate;
        $invoicePayment->patient_id = $invoice->patient_id;
        $invoicePayment->paid_by = $request->paid_by;
        if ($request->paid_by === 'co_payer') {
            $invoicePayment->co_payer_id = $request->co_payer_id;
        }
        $invoicePayment->date = $request->date;
        $invoicePayment->receipt = $request->receipt;
        $invoicePayment->payment_type_id = $request->payment_type_id;
        $invoicePayment->amount = $request->amount;
        $invoicePayment->description = $request->description;
        $invoicePayment->save();
        $invoice->refresh();
        $invoice->balance = $invoice->amount - $invoice->invoicePayments->sum('amount');
        if ($invoice->balance <= 0) {
            $invoice->status = 'paid';
        } elseif ($invoice->balance > 0 && $invoice->balance < $invoice->amount) {
            $invoice->status = 'partially_paid';
        } else {
            $invoice->status = 'unpaid';
        }
        $invoice->cash_balance = $invoice->cash_amount - $invoice->invoicePayments->where('paid_by', 'patient')->sum('amount');
        $invoice->co_payer_balance = $invoice->co_payer_amount - $invoice->invoicePayments->where('paid_by', 'co_payer')->sum('amount');

        $invoice->save();
        event(new InvoicePaymentCreated($invoicePayment));
        activity()
            ->performedOn($invoice)
            ->log('Create Invoice Payment');
        $params = ['invoice' => $invoice->id];
        if ($request->print_payment) {
            $params['print_invoice_payment'] = true;
            $params['invoice_payment_id'] = $invoicePayment->id;
        }
        return redirect()->route('billing.invoices.show', $params)->with('success', 'Invoice payment created successfully.');

    }

    public function show(InvoicePayment $invoicePayment)
    {
        $invoicePayment->load(['invoice', 'currency', 'paymentType', 'coPayer', 'patient']);
        return Inertia::render('InvoicePayments/Show', [
            'invoicePayment' => $invoicePayment
        ]);
    }

    public function edit(InvoicePayment $invoicePayment)
    {
        $invoicePayment->load(['invoice', 'paymentDetail']);
        return Inertia::render('InvoicePayments/Edit', [
            'invoicePayment' => $invoicePayment,
            'paymentTypes' => PaymentType::where('active', 1)->get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'currencies' => Currency::where('active', 1)->get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'coPayers' => CoPayer::get()->transform(function ($coPayer) {
                return [
                    'value' => $coPayer->id,
                    'label' => $coPayer->name,
                ];
            }),
        ]);
    }

    public function update(Request $request, InvoicePayment $invoicePayment)
    {
        $invoice = $invoicePayment->invoice;
        $request->validate([
            'date' => ['required', 'date'],
            'paid_by' => ['required'],
            'amount' => ['required'],
            'payment_type_id' => ['required'],
            'co_payer_id' => ['required_if:paid_by,co_payer'],
        ]);

        $invoicePayment->paymentDetail->payment_type_id = $request->payment_type_id;
        $invoicePayment->paymentDetail->cheque_number = $request->cheque_number;
        $invoicePayment->paymentDetail->receipt = $request->receipt;
        $invoicePayment->paymentDetail->account_number = $request->account_number;
        $invoicePayment->paymentDetail->bank_name = $request->bank_name;
        $invoicePayment->paymentDetail->routing_code = $request->routing_code;
        $invoicePayment->paymentDetail->save();
        $invoicePayment->currency_id = $request->currency_id ?: $invoice->currency_id;
        $invoicePayment->patient_id = $invoice->patient_id;
        $invoicePayment->paid_by = $request->paid_by;
        if ($request->paid_by === 'co_payer') {
            $invoicePayment->co_payer_id = $request->co_payer_id;
        }
        $invoicePayment->date = $request->date;
        $invoicePayment->receipt = $request->receipt;
        $invoicePayment->payment_type_id = $request->payment_type_id;
        $invoicePayment->amount = $request->amount;
        $invoicePayment->description = $request->description;
        $invoicePayment->save();
        $invoice->refresh();
        $invoice->balance = $invoice->amount - $invoice->invoicePayments->sum('amount');
        if ($invoice->balance <= 0) {
            $invoice->status = 'paid';
        } elseif ($invoice->balance > 0 && $invoice->balance < $invoice->amount) {
            $invoice->status = 'partially_paid';
        } else {
            $invoice->status = 'unpaid';
        }
        $invoice->cash_balance = $invoice->cash_amount - $invoice->invoicePayments->where('paid_by', 'patient')->sum('amount');
        $invoice->co_payer_balance = $invoice->co_payer_amount - $invoice->invoicePayments->where('paid_by', 'co_payer')->sum('amount');

        $invoice->save();
        activity()
            ->performedOn($invoicePayment)
            ->log('Update Invoice Payment');
        $params = ['invoice' => $invoice->id];
        if ($request->print_payment) {
            $params['print_invoice_payment'] = true;
            $params['invoice_payment_id'] = $invoicePayment->id;
        }
        return redirect()->route('billing.invoices.show', $params)->with('success', 'Invoice payments updated successfully.');

    }

    public function destroy(InvoicePayment $invoicePayment)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Deleting the demo invoice payment is not allowed.');
        }
        $invoice = $invoicePayment->invoice;
        $invoicePayment->delete();
        $invoice->refresh();
        $invoice->balance = $invoice->amount - $invoice->invoicePayments->sum('amount');
        if ($invoice->balance <= 0) {
            $invoice->status = 'paid';
        } elseif ($invoice->balance > 0 && $invoice->balance < $invoice->amount) {
            $invoice->status = 'partially_paid';
        } else {
            $invoice->status = 'unpaid';
        }
        $invoice->save();
        activity()
            ->performedOn($invoicePayment)
            ->log('Delete Invoice Payment');
        return redirect()->route('billing.invoices.show', $invoice->id)->with('success', 'Invoice payment deleted successfully.');

    }

    public function printPayment(InvoicePayment $invoicePayment)
    {
        $invoicePayment->load(['invoice', 'invoice.patient']);
        return View::make('invoice_payments.print', ['invoicePayment' => $invoicePayment])->render();

    }

    public function pdfPayment(Request $request, InvoicePayment $invoicePayment)
    {
        $invoicePayment->load(['invoice', 'currency', 'paymentType', 'coPayer']);
        $pdf = PDF::loadView('invoice_payments.print', ['invoicePayment' => $invoicePayment]);
        return $pdf->download('payment.pdf');

    }
}
