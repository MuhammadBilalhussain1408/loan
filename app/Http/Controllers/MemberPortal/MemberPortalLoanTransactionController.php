<?php

namespace App\Http\Controllers\MemberPortal;

use App\Events\TransactionUpdated;
use App\Models\Currency;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\PaymentDetail;
use App\Models\PaymentType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Paynow\Payments\Paynow;
use Stripe\Checkout\Session;
use Stripe\Stripe;


class MemberPortalLoanTransactionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Loan $loan)
    {
        if ($loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        $loan->load(['member', 'product', 'currency', 'loanOfficer', 'purpose', 'fund']);
        $balance = $loan->principal;
        //arrears
        $arrearsDays = 0;
        $arrearsAmount = 0;
        $timelyRepayments = 0;
        $principalOverdue = 0;
        $interestOverdue = 0;
        $feesOverdue = 0;
        $penaltiesOverdue = 0;
        $totalDueRepayments = $loan->schedules->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->count();
        $arrearsLastSchedule = $loan->schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
        if (!empty($arrearsLastSchedule)) {
            $overdueSchedules = $loan->schedules->where('due_date', '<=', $arrearsLastSchedule->due_date);
            $principalOverdue = $overdueSchedules->sum('principal') - $overdueSchedules->sum('principal_written_off_derived') - $overdueSchedules->sum('principal_repaid_derived');
            $interestOverdue = $overdueSchedules->sum('interest') - $overdueSchedules->sum('interest_written_off_derived') - $overdueSchedules->sum('interest_repaid_derived') - $overdueSchedules->sum('interest_waived_derived');
            $feesOverdue = $overdueSchedules->sum('fees') - $overdueSchedules->sum('fees_written_off_derived') - $overdueSchedules->sum('fees_repaid_derived') - $overdueSchedules->sum('fees_waived_derived');
            $penaltiesOverdue = $overdueSchedules->sum('penalties') - $overdueSchedules->sum('penalties_written_off_derived') - $overdueSchedules->sum('penalties_repaid_derived') - $overdueSchedules->sum('penalties_waived_derived');
            $arrearsDays = $arrearsDays + Carbon::today()->diffInDays(Carbon::parse($overdueSchedules->sortBy('due_date')->first()->due_date));
        }
        $loan->schedules->transform(function ($item) use (&$balance, &$arrearsDays, &$arrearsAmount, &$timelyRepayments) {
            $item->total = $item->principal - $item->principal_written_off_derived + $item->interest - $item->interest_written_off_derived - $item->interest_waived_derived + $item->fees - $item->fees_written_off_derived - $item->fees_waived_derived + $item->penalties - $item->penalties_written_off_derived - $item->penalties_waived_derived;
            $item->total_paid = $item->principal_repaid_derived + $item->interest_repaid_derived + $item->fees_repaid_derived + $item->penalties_repaid_derived;
            if ($item->total_due <= 0) {
                if (Carbon::parse($item->paid_by_date)->greaterThan(Carbon::parse($item->due_date))) {
                    $item->late_payment = true;
                    $arrearsAmount += $item->total_due;
                } else {
                    $timelyRepayments++;
                    $item->late_payment = false;
                }
            } else {
                if (Carbon::today()->greaterThan(Carbon::parse($item->due_date))) {
                    $item->late_payment = true;
                    $arrearsAmount += $item->total_due;
                } else {
                    $item->late_payment = false;
                }
            }
            $balance = $balance - $item->principal - $item->principal_written_off_derived;
            $item->balance = $balance;
            $item->days = Carbon::parse($item->due_date)->diffInDays(Carbon::parse($item->from_date));
            return $item;
        });
        if ($totalDueRepayments > 0) {
            $timelyRepayments = round($timelyRepayments * 100 / $totalDueRepayments);
        }
        $loan->timely_repayments = $timelyRepayments;
        $loan->arrears_days = $arrearsDays;
        $loan->arrears_amount = $arrearsAmount;
        $loan->principal_overdue = $principalOverdue;
        $loan->interest_overdue = $interestOverdue;
        $loan->fees_overdue = $feesOverdue;
        $loan->penalties_overdue = $penaltiesOverdue;
        $results = LoanTransaction::with(['createdBy', 'type', 'createdBy', 'paymentDetail'])
            ->where('loan_id', $loan->id)
            ->orderBy('id')
            ->get();
        $balance = $loan->principal;
        $results->transform(function ($item) use (&$balance) {
            if ($item->type->name === 'Apply Charges' || $item->type->name === 'Apply Interest') {
                $balance = $balance + $item->amount;
            }
            if ($item->type->name === 'Repayment' || $item->type->name === 'Waive Interest' || $item->type->name === 'Recovery Repayment' || $item->type->name === 'Waive Charges' || $item->type->name === 'Write Off') {
                $balance = $balance - $item->amount;
            }
            $item->balance = $balance;
            return $item;
        });
        return Inertia::render('MemberPortal/Loans/Transactions/Index', [
            'loan' => $loan,
            'results' => $results,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create(Loan $loan)
    {
        if ($loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        return Inertia::render('MemberPortal/Loans/Transactions/Create', [
            'loan' => $loan,
            'paymentTypes' => PaymentType::where('active', 1)->where('is_online', 1)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Loan $loan)
    {
        $request->validate([
            'amount' => ['required', 'numeric'],
            'payment_type_id' => ['required'],
        ]);
        $paymentType = PaymentType::find($request->payment_type_id);
        $currency = Currency::find($loan->currency_id);
        $url = '';
        $success = true;
        $message = '';
        if ($paymentType->system_name === 'paypal') {
            $data = [
                'cmd' => '_xclick',
                'item_number' => $loan->id,
                'item_name' => 'Loan Repayment',
                'category' => 'loan',
                'amount' => $request->amount,
                'business' => $paymentType->options['email'],
                'currency_code' => $paymentType->options['currency_code'],
                'no_shipping' => 1,
                'return' => route('portal.loans.show', ['loan' => $loan->id, 'paid' => 1]),
                'cancel_return' => route('portal.loans.show', ['loan' => $loan->id, 'cancel' => 1]),
                'notify_url' => config('app.app_protocol') . config('app.central_domain') . '/webhooks/paypal',
            ];
            $queryString = http_build_query($data);
            if ($paymentType->options['test_mode']) {
                $url = "https://www.sandbox.paypal.com/cgi-bin/webscr?" . $queryString;
            } else {
                $url = "https://www.paypal.com/cgi-bin/webscr?" . $queryString;
            }
        }
        if ($paymentType->system_name === 'stripe') {
            Stripe::setApiKey($paymentType->options['secret_key']);
            $lineItems = [];
            $lineItems[] = [
                'price_data' => [
                    'product_data' => [
                        'name' => 'Loan Repayment',
                        'description' => 'Loan Repayment',
                    ],
                    'unit_amount' => $request->amount * 100,
                    'currency' => $paymentType->options['currency_code'],
                ],
                'quantity' => 1,//todo:stripe does not allow decimals here
            ];

            try {
                $checkoutSession = Session::create([
                    'member_reference_id' => $loan->id,
                    'payment_intent_data' => [
                        'metadata' => [
                            'loan_id' => $loan->id,
                            'category' => 'loan',
                        ]
                    ],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => route('portal.loans.show', ['loan' => $loan->id, 'paid' => 1]),
                    'cancel_url' => route('portal.loans.show', ['loan' => $loan->id, 'cancel' => 1]),
                ]);
            } catch (\Exception $exception) {
                return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
            }
            $url = $checkoutSession->url;
        }
        if ($paymentType->system_name === 'paynow') {

            if ($currency->code === 'ZWL') {
                $integrationID = $paymentType->options['rtgs_integration_id'];
                $integrationKey = $paymentType->options['rtgs_integration_key'];
            } else {
                $integrationID = $paymentType->options['usd_integration_id'];
                $integrationKey = $paymentType->options['usd_integration_key'];
            }
            $paynow = new Paynow(
                $integrationID,
                $integrationKey,
                route('portal.loans.show',
                    ['loan' => $loan->id, 'paid' => 1]),
                route('webhooks.paynow',
                    ['category' => 'loan']),
            );
            $payment = $paynow->createPayment($loan->id, '');
            $payment->add('Loan Repayment', $request->amount);
            $response = $paynow->send($payment);
            if ($response->success()) {
                $url = $response->redirectUrl();
            } else {
                $success = false;
                $message = $response->errors(false);
                return response()->json(['success' => false, 'message' => $message], 422);
            }
        }
        return response()->json([
            'url' => $url,
            'success' => $success,
            'message' => $message,
        ]);
    }


    public function pdf(LoanTransaction $transaction)
    {
        if ($transaction->loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        $transaction->load(['paymentDetail', 'paymentDetail.payment_type', 'loan', 'type', 'createdBy']);
        $pdf = Pdf::loadView('loan_transaction.pdf', [
            'transaction' => $transaction
        ]);
        return $pdf->download("transaction.pdf");
    }

    public function printTransaction(LoanTransaction $transaction)
    {
        if ($transaction->loan->member_id != Auth::user()->member->id) {
            abort(403, 'Permission Denied');
        }
        $transaction->load(['paymentDetail', 'paymentDetail.payment_type', 'loan', 'type', 'createdBy']);
        return view('loan_transaction.print', [
            'transaction' => $transaction
        ]);
    }

}

