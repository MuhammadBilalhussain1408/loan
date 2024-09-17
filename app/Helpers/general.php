<?php


use App\Models\Branch;
use App\Models\LoanApplication;
use App\Models\LoanApplicationLinkedApprovalStage;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\CommunicationCampaignLog;

use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\Setting;
use App\Models\SmsGateway;
use App\Models\User;
use App\Models\LoanApplicationLinkedCharge;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tjmugova\BluedotSms\BluedotSms;

if (!function_exists('generate_reference')) {
    function generate_reference($id = '', $setting_key = 'invoice_reference_prefix')
    {
        $prefix = '';
        if ($setting_key) {
            if ($setting = Setting::where('setting_key', $setting_key)->first()) {
                $prefix = $setting->setting_value;
            }
        }
        if (strlen($id) < 2) {
            $sequence_number = '00' . $id;
        } elseif (strlen($id) < 3) {
            $sequence_number = '0' . $id;
        } else {
            $sequence_number = $id;
        }
        $random_number = uniqid('', false);
        $reference_format = Setting::where('setting_key', 'invoice_reference_format')->first()->setting_value;
        if ($reference_format == 'Sequence Number') {
            return $prefix . $sequence_number;
        } elseif ($reference_format == 'Random Number') {
            return $prefix . $random_number;
        } elseif ($reference_format == 'YEAR/Sequence Number (SL/2014/001)') {
            return $prefix . date("Y") . '/' . $sequence_number;
        } elseif ($reference_format == 'YEAR/MONTH/Sequence Number (SL/2014/08/001)') {
            return $prefix . date("Y") . '/' . date("m") . '/' . $sequence_number;
        } else {
            return $id;
        }
    }
}
if (!function_exists('send_sms')) {
    /**
     * Send sms to an HTTP API using curl
     * @param string $to The number to send the message to
     * @param string $msg The message to be sent
     * @param null $gateway_id Provide gateway id
     */
    function send_sms(string $to, string $msg, $gateway_id = null)
    {
        if (!$gateway_id) {
            $active_sms_gateway = SmsGateway::find(Setting::where('setting_key',
                'active_sms_gateway')->first()->setting_value);
        } else {
            $active_sms_gateway = SmsGateway::find($gateway_id);
        }
        if ($active_sms_gateway) {
            if ($active_sms_gateway->is_system) {
                if ($active_sms_gateway->system_name === 'bluedot') {
                    $bluedot = app()->make(BluedotSms::class);
                    $response = $bluedot->sendMessage([
                        'to' => $to,
                        'text' => $msg,
                    ]);
                }
                if ($active_sms_gateway->system_name === 'twilio') {
                    $sid = $active_sms_gateway->options['account_sid']; // Your Account SID from www.twilio.com/console
                    $token = $active_sms_gateway->options['auth_token']; // Your Auth Token from www.twilio.com/console
                    $from = $active_sms_gateway->options['from']; // Your Auth Token from www.twilio.com/console
                    $member = new Twilio\Rest\Client($sid, $token);
                    $message = $member->messages->create(
                        $to, // Text this number
                        [
                            'from' => $from, // From a valid Twilio number
                            'body' => $msg
                        ]
                    );

                }
            } else {
                $append = "&";
                $append .= $active_sms_gateway->to_name . "=" . $to;
                $append .= "&" . $active_sms_gateway->msg_name . "=" . urlencode($msg);
                $url = $active_sms_gateway->url . $append;
                //send sms here
                $response = Http::get($url);

            }

        }

    }
}

if (!function_exists('generate_report_friendly_name')) {
    function generate_report_friendly_name($title)
    {
        return Str::replace(['/', ' '], ['', '-'], $title);
    }
}

if (!function_exists('template_replace_tags')) {

    /**
     * Replaces tags in templates
     * @param array $args An array of arguments
     * @return string $body
     */
    function template_replace_tags(array $args)
    {
        $body = $args['body'];
        //member
        if (array_key_exists('member_id', $args)) {
            $member = Member::with(['title', 'profession', 'branch', 'country', 'designation', 'category', 'loanOfficer'])->find($args['member_id']);
            $body = str_replace('{{memberFirstName}}', $member->first_name, $body);
            $body = str_replace('{{memberMiddleName}}', $member->middle_name, $body);
            $body = str_replace('{{memberLastName}}', $member->last_name, $body);
            $body = str_replace('{{memberContactNumber}}', $member->contact_number, $body);
            $body = str_replace('{{memberHomeNumber}}', $member->home_number, $body);
            $body = str_replace('{{memberExternalID}}', $member->external_id, $body);
            $body = str_replace('{{memberEmail}}', $member->email, $body);
            $body = str_replace('{{memberDob}}', $member->dob, $body);
            $body = str_replace('{{memberID}}', $member->id, $body);
            $body = str_replace('{{memberAddress}}', $member->address, $body);
            $body = str_replace('{{memberZip}}', $member->zip, $body);
            $body = str_replace('{{memberState}}', $member->state, $body);
            $body = str_replace('{{memberCity}}', $member->city, $body);
            $body = str_replace('{{memberCountry}}', $member->country->name ?? '', $body);
            $body = str_replace('{{memberProfession}}', $member->profession->name ?? '', $body);
            $body = str_replace('{{memberTitle}}', $member->title->name ?? '', $body);
            $body = str_replace('{{memberBranch}}', $member->branch->name ?? '', $body);
            $body = str_replace('{{memberCategory}}', $member->category->name ?? '', $body);
            $body = str_replace('{{memberDesignation}}', $member->designation->name ?? '', $body);
            $body = str_replace('{{memberLoanOfficer}}', $member->loanOfficer->name ?? '', $body);
            if ($member->gender == 'male') {
                $body = str_replace('{{memberGender}}', 'Male', $body);
            }
            if ($member->gender == 'female') {
                $body = str_replace('{{memberGender}}', 'Female', $body);
            }
        }
        //loan
        if (array_key_exists('loan_id', $args)) {
            $loan = Loan::with(['schedules', 'product', 'branch', 'loanOfficer', 'currency', 'category', 'purpose', 'designation'])->find($args['loan_id']);
            $body = str_replace('{{loan_id}}', $loan->id, $body);
            if ($loan->status == 'pending') {
                $body = str_replace('{{loanStatus}}', 'Pending Approval', $body);
            }
            if ($loan->status == 'submitted') {
                $body = str_replace('{{loanStatus}}', 'Pending Approval', $body);
            }
            if ($loan->status == 'overpaid') {
                $body = str_replace('{{loanStatus}}', 'Overpaid', $body);
            }
            if ($loan->status == 'approved') {
                $body = str_replace('{{loanStatus}}', 'Awaiting Disbursement', $body);
            }
            if ($loan->status == 'active') {
                $body = str_replace('{{loanStatus}}', 'Active', $body);
            }
            if ($loan->status == 'rejected') {
                $body = str_replace('{{loanStatus}}', 'Rejected', $body);
            }
            if ($loan->status == 'withdrawn') {
                $body = str_replace('{{loanStatus}}', 'Withdrawn', $body);
            }
            if ($loan->status == 'written_off') {
                $body = str_replace('{{loanStatus}}', 'Written Off', $body);
            }
            if ($loan->status == 'closed') {
                $body = str_replace('{{loanStatus}}', 'Closed', $body);
            }
            if ($loan->status == 'pending_reschedule') {
                $body = str_replace('{{loanStatus}}', 'Pending Reschedule', $body);
            }
            if ($loan->status == 'rescheduled') {
                $body = str_replace('{{loanStatus}}', 'Rescheduled', $body);
            }
            if (!empty($loan->purpose)) {
                $body = str_replace('{{loanPurpose}}', $loan->purpose->name, $body);
            }
            if (!empty($loan->fund)) {
                $body = str_replace('{{loanFund}}', $loan->fund->name, $body);
            }
            if (!empty($loan->currency)) {
                $body = str_replace('{{loanCurrencyName}}', $loan->currency->name, $body);
                $body = str_replace('{{loanCurrencySymbol}}', $loan->currency->symbol, $body);
            }
            if (!empty($loan->branch)) {
                $body = str_replace('{{loanBranch}}', $loan->branch->name, $body);
            }
            if (!empty($loan->loanOfficer)) {
                $body = str_replace('{{loanOfficer}}', $loan->loanOfficer->name, $body);
            }
            //arrears
            $arrears_days = 0;
            $arrears_amount = 0;
            $arrears_last_schedule = $loan->schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
            if (!empty($arrears_last_schedule)) {
                $overdue_schedules = $loan->schedules->where('due_date', '<=', $arrears_last_schedule->due_date);
                $arrears_amount = $arrears_amount + $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived') + $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived') + $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived') + $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');
                $arrears_days = $arrears_days + Carbon::today()->diffInDays(Carbon::parse($overdue_schedules->sortBy('due_date')->due_date));
            }
            $principal = $loan->schedules->sum('principal') - $loan->schedules->sum('principal_written_off_derived') - $loan->schedules->sum('principal_repaid_derived');
            $interest = $loan->schedules->sum('interest') - $loan->schedules->sum('interest_written_off_derived') - $loan->schedules->sum('interest_repaid_derived') - $loan->schedules->sum('interest_waived_derived');
            $fees = $loan->schedules->sum('fees') - $loan->schedules->sum('fees_written_off_derived') - $loan->schedules->sum('fees_repaid_derived') - $loan->schedules->sum('fees_waived_derived');
            $penalties = $loan->schedules->sum('penalties') - $loan->schedules->sum('penalties_written_off_derived') - $loan->schedules->sum('penalties_repaid_derived') - $loan->schedules->sum('penalties_waived_derived');
            $balance = $principal + $interest + $fees + $penalties;
            $total_due = $loan->schedules->sum('principal') + $loan->schedules->sum('interest') + $loan->schedules->sum('fees') + $loan->schedules->sum('penalties');
            $body = str_replace('{{loanPrincipal}}', number_format($loan->principal, $loan->decimals), $body);
            $body = str_replace('{{loanArrearsAmount}}', number_format($arrears_amount, $loan->decimals), $body);
            $body = str_replace('{{loanArrearsDays}}', $arrears_days, $body);
            $body = str_replace('{{loanDisbursedOnDate}}', $loan->disbursed_on_date, $body);
            $body = str_replace('{{loanFirstPaymentDate}}', $loan->first_payment_date, $body);
            $body = str_replace('{{loanInterestRate}}', $loan->interest_rate, $body);
            $body = str_replace('{{loanClosedOnDate}}', $loan->closed_on_date, $body);
            $body = str_replace('{{loanClosedNotes}}', $loan->closed_notes, $body);
            $body = str_replace('{{loanExternalID}}', $loan->external_id, $body);
            $body = str_replace('{{loanAccountNumber}}', $loan->account_number, $body);
            $body = str_replace('{{loanBalance}}', number_format($balance, $loan->decimals), $body);
            $body = str_replace('{{loanTotalInterest}}', number_format($loan->schedules->sum('interest'), $loan->decimals), $body);
            $body = str_replace('{{loanTotalFees}}', number_format($loan->schedules->sum('fees'), $loan->decimals), $body);
            $body = str_replace('{{loanTotalPenalties}}', number_format($loan->schedules->sum('penalties'), $loan->decimals), $body);
            $body = str_replace('{{loanOutstandingInterest}}', number_format($interest, $loan->decimals), $body);
            $body = str_replace('{{loanOutstandingFees}}', number_format($fees, $loan->decimals), $body);
            $body = str_replace('{{loanOutstandingPenalties}}', number_format($penalties, $loan->decimals), $body);
            $body = str_replace('{{loanOutstandingPrincipal}}', number_format($principal, $loan->decimals), $body);
            $body = str_replace('{{loanTotalPayments}}', number_format($total_due - $balance, $loan->decimals), $body);
            $body = str_replace('{{loanTotalDue}}', number_format($total_due, $loan->decimals), $body);
            $body = str_replace('{{loanTerm}}', $loan->loan_term, $body);
            $body = str_replace('{{loanID}}', $loan->id, $body);
        }
        //loan transaction
        if (array_key_exists('loan_transaction_id', $args)) {
            $loan_transaction = LoanTransaction::with(['paymentDetail', 'loan', 'createdBy'])->find($args['loan_transaction_id']);
            (empty($loan_transaction->loan)) ? $decimals = 2 : $decimals = $loan_transaction->loan->decimals;
            $body = str_replace('{{loanTransactionAmount}}', number_format($loan_transaction->amount, $decimals), $body);
            $body = str_replace('{{loanTransactionCredit}}', number_format($loan_transaction->credit, $decimals), $body);
            $body = str_replace('{{loanTransactionDebit}}', number_format($loan_transaction->debit, $decimals), $body);
            $body = str_replace('{{loanTransactionSubmittedOn}}', $loan_transaction->submitted_on, $body);
            $body = str_replace('{{loanTransactionCreatedOn}}', $loan_transaction->created_on, $body);
            $body = str_replace('{{loanTransactionReceiptNumber}}', $loan_transaction->paymentDetail->receipt ?? '', $body);
            $body = str_replace('{{loanTransactionChequeNumber}}', $loan_transaction->paymentDetail->cheque_number ?? '', $body);
            $body = str_replace('{{loanTransactionAccountNumber}', $loan_transaction->paymentDetail->account_number ?? '', $body);
            $body = str_replace('{{loanTransactionBankName}}', $loan_transaction->paymentDetail->bank_name ?? '', $body);
            $body = str_replace('{{loanTransactionRoutingCode}}', $loan_transaction->paymentDetail->routing_code ?? '', $body);
            $body = str_replace('{{loanTransactionDescription}', $loan_transaction->paymentDetail->description ?? '', $body);
            $body = str_replace('{{loanTransactionPaymentType}}', $loan_transaction->paymentDetail->payment_type->name ?? '', $body);
            $body = str_replace('{{loanTransactionCreatedBy}}', $loan_transaction->createdBy->name ?? '', $body);

        }
        if (array_key_exists('loan_application_id', $args)) {
            $application = LoanApplication::with(['category', 'designation', 'product', 'loanOfficer'])->find($args['loan_application_id']);
            $decimals = $application->decimals;
            $body = str_replace('{{loanApplicationAppliedAmount}}', number_format($application->applied_amount, $decimals), $body);
            $body = str_replace('{{loanApplicationApprovedAmount}}', number_format($application->approved_amount, $decimals), $body);
            $body = str_replace('{{loanApplicationInterestRate}}', $application->interest_rate, $body);
            $body = str_replace('{{loanApplicationID}}', $application->id, $body);
        }
        if (array_key_exists('loan_application_linked_approval_stage_id', $args)) {
            $approvalStage = LoanApplicationLinkedApprovalStage::with(['stage'])->find($args['loan_application_linked_approval_stage_id']);
            $body = str_replace('{{loanApplicationApprovalStageStatus}}', $approvalStage->status, $body);
            $body = str_replace('{{loanApplicationApprovalStageName}}', $approvalStage->stage->name??'', $body);
            $body = str_replace('{{loanApplicationApprovalStageNotes}}', $approvalStage->description, $body);
            $body = str_replace('{{loanApplicationApprovalStageID}}', $approvalStage->id, $body);
        }
        if (array_key_exists('user_id', $args)) {
            $user = User::find($args['user_id']);
            $body = str_replace('{{userFirstName}}', $user->first_name, $body);
            $body = str_replace('{{userLastName}}', $user->last_name, $body);
        }
        //loan repayment schedule
        if (array_key_exists('loan_repayment_schedule_id', $args)) {
            $loan_repayment_schedule = LoanRepaymentSchedule::with('loan')->find($args['loan_repayment_schedule_id']);
            (empty($loan_repayment_schedule->loan)) ? $decimals = 2 : $decimals = $loan_repayment_schedule->loan->decimals;
            $body = str_replace('{{loanRepaymentScheduleAmount}}', number_format($loan_repayment_schedule->total_due, $decimals), $body);
            $body = str_replace('{{loanRepaymentScheduleDueDate}}', $loan_repayment_schedule->due_date, $body);
        }
        return $body;
    }
}
if (!function_exists('log_campaign')) {

    /**
     * Logs a communication campaign
     * @param array $args
     */
    function log_campaign(array $args)
    {
        $communication_campaign_log = new CommunicationCampaignLog();
        foreach ($args as $key => $value) {
            $communication_campaign_log->$key = $value;
        }
        $communication_campaign_log->save();

    }
}
if (!function_exists('determine_period_interest_rate')) {

    /**
     * @param $default_interest_rate
     * @param $repayment_frequency_type
     * @param $interest_rate_type
     * @param int $days_in_year
     * @param int $days_in_month
     * @param int $weeks_in_year
     * @param int $weeks_in_month
     * @return float
     */
    function determine_period_interest_rate($default_interest_rate, $repayment_frequency_type, $interest_rate_type, $repayment_frequency = 1, $days_in_year = 365, $days_in_month = 30, $weeks_in_year = 52, $weeks_in_month = 4)
    {
        if ($interest_rate_type === 'principal') {
            return $default_interest_rate / 100;
        }
        $interest_rate = $default_interest_rate;
        if ($repayment_frequency_type == "days") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / $days_in_year;
            }
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate / $days_in_month;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate / 7;
            }
        }
        if ($repayment_frequency_type == "weeks") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / $days_in_year;
            }
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate / $weeks_in_month;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * 7;
            }
        }
        if ($repayment_frequency_type == "months") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / 12;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate * $weeks_in_month;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * $days_in_month;
            }
        }
        if ($repayment_frequency_type == "years") {
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate * 12;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate * $weeks_in_year;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * $days_in_year;
            }
        }
        return $interest_rate * $repayment_frequency / 100;
    }
}
if (!function_exists('determine_amortized_payment')) {

    /**
     * @param $default_interest_rate
     * @param $repayment_frequency_type
     * @param $interest_rate_type
     * @param int $days_in_year
     * @param int $days_in_month
     * @param int $weeks_in_year
     * @param int $weeks_in_month
     * @return float
     */
    function determine_amortized_payment($interest_rate, $balance, $period)
    {

        return ($interest_rate * $balance * pow((1 + $interest_rate), $period)) / (pow((1 + $interest_rate),
                    $period) - 1);
    }
}
if (!function_exists('compare_multi_dimensional_array')) {
    function compare_multi_dimensional_array($array1, $array2)
    {
        $result = array();
        foreach ($array1 as $key => $value) {
            if (!is_array($array2) || !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }
            if (is_array($value)) {
                $recursiveArrayDiff = compare_multi_dimensional_array($value, $array2[$key]);
                if (count($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }
                continue;
            }
            if ($value != $array2[$key]) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
if (!function_exists('generate_loan_reference')) {
    function generate_loan_reference($setting_key = '', $id = '')
    {
        $prefix = '';
        if ($setting_key) {
            if ($setting = Setting::where('setting_key', $setting_key)->first()) {
                $prefix = $setting->setting_value;
            }
        }
        if (strlen($id) < 2) {
            $sequence_number = '00' . $id;
        } elseif (strlen($id) < 3) {
            $sequence_number = '0' . $id;
        } else {
            $sequence_number = $id;
        }
        $random_number = uniqid();
        $reference_format = Setting::where('setting_key', 'core.reference_format')->first()->setting_value;
        if ($reference_format == 'Sequence Number') {
            return $prefix . $sequence_number;
        } elseif ($reference_format == 'Random Number') {
            return $prefix . $random_number;
        } elseif ($reference_format == 'YEAR/Sequence Number (SL/2014/001)') {
            return $prefix . date("Y") . '/' . $sequence_number;
        } elseif ($reference_format == 'YEAR/MONTH/Sequence Number (SL/2014/08/001)') {
            return $prefix . date("Y") . '/' . date("m") . '/' . $sequence_number;
        } else {
            return $id;
        }
    }
}
if (!function_exists('generate_savings_reference')) {
    function generate_savings_reference($setting_key = '', $savings = '')
    {
        $prefix = '';
        if ($setting_key) {
            if ($setting = Setting::where('setting_key', $setting_key)->first()) {
                $prefix = $setting->setting_value;
            }
        }
        if (strlen($savings->id) < 2) {
            $sequence_number = '00' . $savings->id;
        } elseif (strlen($savings->id) < 3) {
            $sequence_number = '0' . $savings->id;
        } else {
            $sequence_number = $savings->id;
        }
        $random_number = uniqid();
        $reference_format = Setting::where('setting_key', 'savings_reference_format')->first()->setting_value;
        if ($reference_format == 'Sequence Number') {
            return $prefix . $sequence_number;
        } elseif ($reference_format == 'Random Number') {
            return $prefix . $random_number;
        } elseif ($reference_format == 'YEAR/Sequence Number (SL/2014/001)') {
            return $prefix . date("Y") . '/' . $sequence_number;
        } elseif ($reference_format == 'YEAR/MONTH/Sequence Number (SL/2014/08/001)') {
            return $prefix . date("Y") . '/' . date("m") . '/' . $sequence_number;
        } elseif ($reference_format == 'Branch Product Sequence Number') {
            return $prefix . $savings->branch_id . $savings->savings_product_id . $sequence_number;
        } else {
            return $savings->id;
        }
    }
}
if (!function_exists('get_next_week_day')) {
    function get_next_week_day($date)
    {
        if (Carbon::parse($date)->isWeekday()) {
            return $date;
        }
        $newDate = Carbon::parse($date)->addDay()->format('Y-m-d');
        get_next_week_day($newDate);
    }
}
if (!function_exists('get_next_non_holiday_day')) {
    function get_next_non_holiday_day($date)
    {
        if (!in_array(Carbon::parse($date)->format('d'), config('loan.holidays')[Carbon::parse($date)->format('F')])) {
            return $date;
        }
        $newDate = Carbon::parse($date)->addDay()->format('Y-m-d');
        get_next_non_holiday_day($newDate);
    }
}

if (!function_exists('database_exists')) {
    function database_exists($name): bool
    {
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $db = DB::select($query, [$name]);
        if (empty($db)) {
            return false;
        } else {
            return true;
        }
    }
}
/**
 * Calculate the previous range and calculate any short-cuts.
 *
 * @param string|int $range
 * @return array<int, \Carbon\CarbonInterface>
 */
function previousRange($range)
{
    if ($range == 'TODAY') {
        return [
            CarbonImmutable::now(config('app.timezone'))->subDay()->startOfDay(),
            CarbonImmutable::now(config('app.timezone'))->subDay()->endOfDay(),
        ];
    }

    if ($range == 'YESTERDAY') {
        return [
            CarbonImmutable::now(config('app.timezone'))->subDays(2)->startOfDay(),
            CarbonImmutable::now(config('app.timezone'))->subDays(2)->endOfDay(),
        ];
    }

    if ($range == 'THIS_WEEK') {
        return [
            CarbonImmutable::now(config('app.timezone'))->subWeek()->startOfWeek(),
            CarbonImmutable::now(config('app.timezone'))->subWeek()->endOfWeek(),
        ];
    }

    if ($range == 'MTD') {
        return [
            CarbonImmutable::now(config('app.timezone'))->subMonthWithoutOverflow()->startOfMonth(),
            CarbonImmutable::now(config('app.timezone'))->subMonthWithoutOverflow(),
        ];
    }

    if ($range == 'QTD') {
        return previousQuarterRange(config('app.timezone'));
    }

    if ($range == 'YTD') {
        return [
            CarbonImmutable::now(config('app.timezone'))->subYear()->startOfYear(),
            CarbonImmutable::now(config('app.timezone'))->subYear(),
        ];
    }

    return [
        CarbonImmutable::now(config('app.timezone'))->subDays($range * 2),
        CarbonImmutable::now(config('app.timezone'))->subDays($range)->subSecond(),
    ];
}

/**
 * Calculate the previous quarter range.
 *
 * @return array<int, \Carbon\CarbonImmutable>
 */
function previousQuarterRange()
{
    return [
        CarbonImmutable::now(config('app.timezone'))->subQuarterWithOverflow()->startOfQuarter(),
        CarbonImmutable::now(config('app.timezone'))->subQuarterWithOverflow()->subSecond(),
    ];
}

/**
 * Calculate the current range and calculate any short-cuts.
 *
 * @param string|int $range
 * @return array<int, \Carbon\CarbonInterface>
 */
function currentRange($range)
{
    if ($range == 'TODAY') {
        return [
            CarbonImmutable::now(config('app.timezone'))->startOfDay(),
            CarbonImmutable::now(config('app.timezone'))->endOfDay(),
        ];
    }

    if ($range == 'YESTERDAY') {
        return [
            CarbonImmutable::now(config('app.timezone'))->subDay()->startOfDay(),
            CarbonImmutable::now(config('app.timezone'))->subDay()->endOfDay(),
        ];
    }

    if ($range == 'THIS_WEEK') {
        return [
            CarbonImmutable::now(config('app.timezone'))->startOfWeek(),
            CarbonImmutable::now(config('app.timezone'))->endOfWeek(),
        ];
    }

    if ($range == 'MTD') {
        return [
            CarbonImmutable::now(config('app.timezone'))->startOfMonth(),
            CarbonImmutable::now(config('app.timezone')),
        ];
    }

    if ($range == 'QTD') {
        return currentQuarterRange(config('app.timezone'));
    }

    if ($range == 'YTD') {
        return [
            CarbonImmutable::now(config('app.timezone'))->startOfYear(),
            CarbonImmutable::now(config('app.timezone')),
        ];
    }

    return [
        CarbonImmutable::now(config('app.timezone'))->subDays($range),
        CarbonImmutable::now(config('app.timezone')),
    ];
}

/**
 * Calculate the previous quarter range.
 *
 * @return array<int, \Carbon\CarbonImmutable>
 */
function currentQuarterRange()
{
    return [
        CarbonImmutable::now(config('app.timezone'))->startOfQuarter(),
        CarbonImmutable::now(config('app.timezone')),
    ];
}

function generate_loan_application_schedule(LoanApplication $application)
{
  //  dd($application);
    $product = $application->product;
    $loan_details = [];
    $admincharges= $application->admin_charges;
    $loan_details['principal'] = $application->applied_amount;
    $loan_details['disbursement_date'] = Carbon::today()->format('Y-m-d');
    $schedules = [];
    $loan_principal = $application->applied_amount;
    $interest_rate = determine_period_interest_rate($application->interest_rate, $application->repayment_frequency_type, $application->interest_rate_type);
    $balance = round($loan_principal, $application->decimals);
    $period = floor($application->loan_term / $application->repayment_frequency);
    $payment_from_date = Carbon::today()->format('Y-m-d');
    $next_payment_date = Carbon::today()->format('Y-m-d');
    $total_principal = 0;
    $total_interest = 0;
    $total_days = 0;
    $totaladmincharges = 0;
    for ($i = 1; $i <= $period; $i++) {
        $schedule = [];

        $schedule['installment'] = $i;

        $schedule['due_date'] = $next_payment_date;
        $schedule['from_date'] = $payment_from_date;
        $schedule['fees'] = 0;
        $schedule['days'] = Carbon::parse($schedule['due_date'])->diffInDays(Carbon::parse($schedule['from_date']));
        $total_days = $total_days + $schedule['days'];
        //flat  method
        if ($application->interest_methodology == 'flat') {
            $principal = round($loan_principal / $period, $application->decimals);
            $interest = round($interest_rate * $loan_principal, $application->decimals);
            if ($application->grace_on_interest_charged >= $i) {
                $schedule['interest'] = 0;
            } else {
                $schedule['interest'] = $interest;
            }
            if ($i == $period) {
                //account for values lost during rounding
                $schedule['principal'] = round($balance, $application->decimals);
            } else {
                $schedule['principal'] = $principal;
            }
            //determine next balance
            $balance = ($balance - $principal);
        }
        //reducing balance
        if ($application->interest_methodology == 'declining_balance') {
            if ($application->amortization_method == 'equal_installments') {
                $amortized_payment = round(determine_amortized_payment($interest_rate, $loan_principal, $period), $application->decimals);
                //determine if we have grace period for interest
                $interest = round($interest_rate * $balance, $application->decimals);
                $principal = round(($amortized_payment - $interest), $application->decimals);
                if ($application->grace_on_interest_charged >= $i) {
                    $schedule['interest'] = 0;
                } else {
                    $schedule['interest'] = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $schedule['principal'] = round($balance, $application->decimals);
                    $balance = 0;
                } else {
                    $schedule['principal'] = $principal;
                    $balance = ($balance - $principal);
                }
            }
            if ($application->amortization_method == 'equal_principal_payments') {
                $principal = round($loan_principal / $period, $application->decimals);
                //determine if we have grace period for interest
                $interest = round($interest_rate * $balance, $application->decimals);
                if ($application->grace_on_interest_charged >= $i) {
                    $schedule['interest'] = 0;
                } else {
                    $schedule['interest'] = $interest;
                }

                if ($i == $period) {
                    //account for values lost during rounding
                    $schedule['principal'] = round($balance, $application->decimals);
                    $balance = 0;
                } else {
                    $schedule['principal'] = $principal;
                    $balance = ($balance - $principal);
                }
                //determine next balance

            }

        }
        $schedule['balance'] = (double)$balance;
        $fee=($schedule['principal'] * $admincharges)/100;
        $schedule['fees'] = $fee;
        $totaladmincharges = $totaladmincharges + $fee;
        $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
        $next_payment_date = Carbon::parse($next_payment_date)->add($application->repayment_frequency, $application->repayment_frequency_type)->format("Y-m-d");
        $total_principal = $total_principal + $schedule['principal'];
        $total_interest = $total_interest + $schedule['interest'];
        $schedules[] = $schedule;
    }

    $installment_fees = 0;
    $disbursement_fees = 0;

    foreach ($application->charges as $key) {
        //disbursement
       // $admincharges = $admincharges + $key->amount;
       // dd($admincharges);
        if ($key->charge->type->name === 'Disbursement') {
            $amount = 0;
            if ($key->charge->loan_charge_option_id == 1) {
                $amount = $key->charge->amount;

            }
            if ($key->charge->loan_charge_option_id == 2) {
                $amount = round(($key->charge->amount * $total_principal / 100), $application->decimals);
            }
            if ($key->charge->loan_charge_option_id == 3) {
                $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $application->decimals);

            }
            if ($key->charge->loan_charge_option_id == 4) {
                $amount = round(($key->charge->amount * $total_interest / 100), $application->decimals);

            }
            if ($key->charge->loan_charge_option_id == 5) {
                $amount = round(($key->charge->amount * $total_principal / 100), $application->decimals);

            }
            if ($key->charge->loan_charge_option_id == 6) {
                $amount = round(($key->charge->amount * $total_principal / 100), $application->decimals);

            }
            if ($key->charge->loan_charge_option_id == 7) {
                $amount = round(($key->charge->amount * $loan_principal / 100), $application->decimals);

            }
            $disbursement_fees = $disbursement_fees + $amount;
        }
        //installment_fee

        if ($key->charge->type->name === 'Installment Fees') {
            $amount = 0;
            if ($key->charge->option->name === 'Flat') {
                $amount = $key->charge->amount;
            }
            if ($key->charge->option->name === 'Principal due on installment') {
                $amount = round(($key->charge->amount * $total_principal / 100), $application->decimals);
            }
            if ($key->charge->option->name === 'Principal + Interest due on installment') {
                $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $application->decimals);
            }
            if ($key->charge->option->name === 'Interest due on installment') {
                $amount = round(($key->charge->amount * $total_interest / 100), $application->decimals);
            }
            if ($key->charge->option->name === 'Total Outstanding Loan Principal') {
                $amount = round(($key->charge->amount * $total_principal / 100), $application->decimals);
            }
            if ($key->charge->option->name === 'Percentage of Original Loan Principal per Installment') {
                $amount = round(($key->charge->amount * $total_principal / 100), $application->decimals);
            }
            if ($key->charge->option->name === 'Original Loan Principal') {
                $amount = round(($key->charge->amount * $loan_principal / 100), $application->decimals);
            }
            // if ($key->charge->option->name === 'Admin Fee Charges') {
            //     $amount = round($admincharges, $application->decimals);
            // }

            $installment_fees = $installment_fees + $amount ;
            //add the charges to the schedule

            foreach ($schedules as &$temp) {
               // $totaladmincharges = $totaladmincharges + $admincharges;
               // $temp['fees'] = $admincharges;
                if ($key->charge->option->name === 'Principal due on installment') {
                    $temp['fees'] = $temp['fees'] +  round(($key->charge->amount * $temp['principal'] / 100), $application->decimals);
                } elseif ($key->charge->option->name === 'Principal + Interest due on installment') {
                    $temp['fees'] = $temp['fees'] + round(($key->charge->amount * ($temp['interest'] + $temp['principal']) / 100), $application->decimals);
                } elseif ($key->charge->option->name === 'Interest due on installment') {
                    $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['interest'] / 100), $application->decimals);
                }
            // //  elseif ($key->charge->option->name === 'Admin Fee Charges') {
            //     $temp['fees'] = $temp['fees'] + round($admincharges, $application->decimals);
            // }
            else {
                    $temp['fees'] = $temp['fees'] + $key->charge->amount;
                }

            }

        }

    }
    foreach ($schedules as $key => $value) {
        $schedules[$key]['total_due'] = $value['principal'] + $value['interest'] + $value['fees'] ;
    }
    $loan_details['total_days'] = $total_days;
    $loan_details['total_principal'] = $total_principal;
    $loan_details['principal'] = $loan_details['principal'];
    $loan_details['total_interest'] = $total_interest;
    $loan_details['decimals'] = $application->decimals;
    $loan_details['disbursement_fees'] = $disbursement_fees;
    $loan_details['total_fees'] = $disbursement_fees + $installment_fees + $totaladmincharges;
    $loan_details['total_due'] = $disbursement_fees + $installment_fees + $total_interest + $total_principal  + $totaladmincharges;
    $loan_details['maturity_date'] = $next_payment_date;

    // dd($loan_details,$totaladmincharges, $total_principal, $schedules);
    return [
        'loan_details' => $loan_details,
        'schedules' => $schedules,
    ];
}
