<?php

namespace App\Http\Controllers;

use App\Events\DonationCreated;
use App\Events\InvoicePaymentCreated;
use App\Events\LoanTransactionCreated;
use App\Events\TransactionUpdated;
use App\Http\Controllers\Controller;


use App\Models\AdminInvoicePayment;
use App\Models\Currency;
use App\Models\Donation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\PaymentDetail;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Paynow\Payments\Paynow;

use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class WebhooksController extends Controller
{
    public function __construct()
    {

    }


    public function stripeWebhook(Request $request)
    {
        $paymentType = PaymentType::where('system_name', 'stripe')->first();
        $payload = $request->getContent();
        $sigHeader = $request->header('STRIPE_SIGNATURE');
        $endpointSecret = $paymentType->options['webhook_signing_secret'];
        $event = null;
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            Log::error($e);
            exit();
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            //http_response_code(400);
            http_response_code(200);
            Log::error($e);
            exit();
        }
        if ($event->type == "payment_intent.succeeded") {
            $intent = $event->data->object;
            $charges = $intent->charges->data[0];
            $category = $intent->metadata->category;
            if ($category === 'loan') {
                $loan = Loan::with(['member'])
                    ->find($intent->metadata->loan_id);
                if ($loan) {
                    //store the transaction
                    $paymentDetail = new PaymentDetail();
                    $paymentDetail->payment_type_id = $paymentType->id;
                    $paymentDetail->transaction_type = 'loan_transaction';
                    $paymentDetail->receipt = $intent->id;
                    $paymentDetail->description = 'Online payment';
                    $paymentDetail->save();
                    $transaction = new LoanTransaction();
                    $transaction->loan_id = $loan->id;
                    $transaction->payment_detail_id = $paymentDetail->id;
                    $transaction->name = 'Repayment';
                    $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Repayment')->first()->id;
                    $transaction->submitted_on = $request->date;
                    $transaction->created_on = date("Y-m-d");
                    $transaction->amount = $intent->amount / 100;
                    $transaction->credit = $intent->amount / 100;
                    $transaction->reversible = 1;
                    $transaction->online_transaction = 1;
                    $transaction->save();

                    //fire transaction updated event
                    event(new LoanTransactionCreated($transaction));
                    event(new TransactionUpdated($loan));
                }
            }
            printf("Succeeded: %s", $intent->id);
            http_response_code(200);
            exit();
        } elseif ($event->type == "payment_intent.payment_failed") {
            $intent = $event->data->object;
            $error_message = $intent->last_payment_error ? $intent->last_payment_error->message : "";
            printf("Failed: %s, %s", $intent->id, $error_message);
            http_response_code(200);
            exit();
        }

    }


    public function paypalWebhook(Request $request)
    {
        $paymentType = PaymentType::where('system_name', 'paypal')->first();
        if ($paymentType->options['test_mode']) {
            $url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            $url = "https://ipnpb.paypal.com/cgi-bin/webscr";
        }
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
        $req = 'cmd=_notify-validate';

        foreach ($myPost as $key => $value) {
            $value = urlencode($value);
            $req .= "&$key=$value";
        }
        $response = Http::withUserAgent('PHP-IPN-Verification-Script')
            ->withHeaders([
                'Connection' => 'Close'
            ])
            ->withBody($req, '')
            ->post($url);
        if (str_contains($response->body(), 'VERIFIED')) {
            $reference = $request->item_number;
            $category = $request->category;
            if (strtolower($request->receiver_email) != strtolower($paymentType->options['email'])) {
                abort(422, "Invalid Business Email: $req" . PHP_EOL);
            }
            if ($category === 'loan') {
                $loan = Loan::with(['member'])
                    ->find($request->loan_id);
                if ($loan) {
                    //store the transaction
                    $paymentDetail = new PaymentDetail();
                    $paymentDetail->payment_type_id = $paymentType->id;
                    $paymentDetail->transaction_type = 'loan_transaction';
                    $paymentDetail->receipt = $request->txn_id;
                    $paymentDetail->description = 'Online payment';
                    $paymentDetail->save();
                    $transaction = new LoanTransaction();
                    $transaction->loan_id = $loan->id;
                    $transaction->payment_detail_id = $paymentDetail->id;
                    $transaction->name = 'Repayment';
                    $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Repayment')->first()->id;
                    $transaction->submitted_on = $request->date;
                    $transaction->created_on = date("Y-m-d");
                    $transaction->amount = $request->mc_gross;
                    $transaction->credit = $request->mc_gross;
                    $transaction->reversible = 1;
                    $transaction->online_transaction = 1;
                    $transaction->save();

                    //fire transaction updated event
                    event(new LoanTransactionCreated($transaction));
                    event(new TransactionUpdated($loan));
                }
            }
        }
    }


    public function paynowWebhook(Request $request)
    {
        $category = $request->category;
        $reference = $request->reference;
        $paymentType = PaymentType::where('system_name', 'paynow')->first();
        if ($category === 'loan') {
            $loan = Loan::with(['member'])
                ->find($reference);
            if ($loan) {
                if ($loan->currency->code === 'ZWL') {
                    $intergrationID = $paymentType->options['rtgs_integration_id'];
                    $intergrationKey = $paymentType->options['rtgs_integration_key'];
                } else {
                    $intergrationID = $paymentType->options['usd_integration_id'];
                    $intergrationKey = $paymentType->options['usd_integration_key'];
                }
                $paynow = new Paynow(
                    $intergrationID,
                    $intergrationKey,
                    route('portal.loans.show',
                        ['loan' => $loan->id, 'paid' => 1]),
                    route('webhooks.paynow',
                        ['category' => 'loan']),

                );
                $status = $paynow->processStatusUpdate();
                if ($status->paid()) {
                    //store the transaction
                    $paymentDetail = new PaymentDetail();
                    $paymentDetail->payment_type_id = $paymentType->id;
                    $paymentDetail->transaction_type = 'loan_transaction';
                    $paymentDetail->receipt = $status->paynowReference();
                    $paymentDetail->description = 'Online payment';
                    $paymentDetail->save();
                    $transaction = new LoanTransaction();
                    $transaction->loan_id = $loan->id;
                    $transaction->payment_detail_id = $paymentDetail->id;
                    $transaction->name = 'Repayment';
                    $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Repayment')->first()->id;
                    $transaction->submitted_on = $request->date;
                    $transaction->created_on = date("Y-m-d");
                    $transaction->amount = $status->amount();
                    $transaction->credit = $status->amount();
                    $transaction->reversible = 1;
                    $transaction->online_transaction = 1;
                    $transaction->save();

                    //fire transaction updated event
                    event(new LoanTransactionCreated($transaction));
                    event(new TransactionUpdated($loan));
                }
            }
            printf("Succeeded: %s", $status->paynowReference());
            http_response_code(200);
            exit();
        }
    }
}
