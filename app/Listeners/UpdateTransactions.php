<?php

namespace App\Listeners;

use App\Events\TransactionUpdated;
use App\Models\JournalEntry;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class UpdateTransactions implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TransactionUpdated $event
     * @return void
     */
    public function handle(TransactionUpdated $event)
    {
        $loan = $event->loan;
        $schedules = $loan->schedules;
        $original_transactions = LoanTransaction::where('loan_id', $loan->id)->whereIn('loan_transaction_type_id', LoanTransactionType::whereIn('name', ['Repayment', 'Write Off', 'Recovery Repayment'])->get()->pluck('id')->toArray())->orderBy('submitted_on', 'asc')->orderBy('id', 'asc')->get();
        $transactions = LoanTransaction::where('loan_id', $loan->id)->whereIn('loan_transaction_type_id', LoanTransactionType::whereIn('name', ['Repayment', 'Write Off', 'Recovery Repayment'])->get()->pluck('id')->toArray())->orderBy('submitted_on', 'asc')->orderBy('id', 'asc')->get();
        //set paid derived to zero in repayment schedules
        foreach ($schedules as &$schedule) {
            $schedule->total_due = ($schedule->principal - $schedule->principal_written_off_derived - $schedule->principal_repaid_derived) + ($schedule->interest - $schedule->interest_written_off_derived - $schedule->interest_repaid_derived - $schedule->interest_waived_derived) + ($schedule->fees - $schedule->fees_written_off_derived - $schedule->fees_repaid_derived - $schedule->fees_waived_derived) + ($schedule->penalties - $schedule->penalties_written_off_derived - $schedule->penalties_repaid_derived - $schedule->penalties_waived_derived);
            $schedule->total = $schedule->total_due;
            $schedule->balance = $schedule->total_due;
            $schedule->principal_repaid_derived = 0;
            $schedule->fees_repaid_derived = 0;
            $schedule->interest_repaid_derived = 0;
            $schedule->penalties_repaid_derived = 0;

            $schedule->save();
        }
        foreach ($transactions as &$transaction) {
            $amount = $transaction->amount;
            $principal_repaid_derived = 0;
            $interest_repaid_derived = 0;
            $fees_repaid_derived = 0;
            $penalties_repaid_derived = 0;
            //loop through repayment schedules
            foreach ($schedules as &$schedule) {
                if ($amount <= 0) {
                    break;
                }
                $principal = $schedule->principal - $schedule->principal_written_off_derived - $schedule->principal_repaid_derived;
                $interest = $schedule->interest - $schedule->interest_written_off_derived - $schedule->interest_repaid_derived - $schedule->interest_waived_derived;
                $fees = $schedule->fees - $schedule->fees_written_off_derived - $schedule->fees_repaid_derived - $schedule->fees_waived_derived;
                $penalties = $schedule->penalties - $schedule->penalties_written_off_derived - $schedule->penalties_repaid_derived - $schedule->penalties_waived_derived;
                $due = $principal + $interest + $fees + $penalties;
                if ($due <= 0) {
                    continue;
                }
                //allocate the payment
                if ($loan->loan_transaction_processing_strategy_id == 1) {
                    //penalties
                    if ($amount >= $penalties && $penalties > 0) {
                        $schedule->penalties_repaid_derived = $schedule->penalties_repaid_derived + $penalties;
                        $penalties_repaid_derived = $penalties_repaid_derived + $penalties;
                        $amount = $amount - $penalties;
                    } elseif ($amount < $penalties && $penalties > 0) {
                        $schedule->penalties_repaid_derived = $schedule->penalties_repaid_derived + $amount;
                        $penalties_repaid_derived = $penalties_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //fees
                    if ($amount >= $fees && $fees > 0) {
                        $schedule->fees_repaid_derived = $schedule->fees_repaid_derived + $fees;
                        $fees_repaid_derived = $fees_repaid_derived + $fees;
                        $amount = $amount - $fees;
                    } elseif ($amount < $fees && $fees > 0) {
                        $schedule->fees_repaid_derived = $schedule->fees_repaid_derived + $amount;
                        $fees_repaid_derived = $fees_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //interest
                    if ($amount >= $interest && $interest > 0) {
                        $schedule->interest_repaid_derived = $schedule->interest_repaid_derived + $interest;
                        $interest_repaid_derived = $interest_repaid_derived + $interest;
                        $amount = $amount - $interest;
                    } elseif ($amount < $interest && $interest > 0) {
                        $schedule->interest_repaid_derived = $schedule->interest_repaid_derived + $amount;
                        $interest_repaid_derived = $interest_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //principal
                    if ($amount >= $principal && $principal > 0) {
                        $schedule->principal_repaid_derived = $schedule->principal_repaid_derived + $principal;
                        $principal_repaid_derived = $principal_repaid_derived + $principal;
                        $amount = $amount - $principal;

                    } elseif ($amount < $principal && $principal > 0) {
                        $schedule->principal_repaid_derived = $schedule->principal_repaid_derived + $amount;
                        $principal_repaid_derived = $principal_repaid_derived + $amount;
                        $amount = 0;
                    }

                }
                if ($loan->loan_transaction_processing_strategy_id == 2) {

                    //principal
                    if ($amount >= $principal && $principal > 0) {
                        $schedule->principal_repaid_derived = $schedule->principal_repaid_derived + $principal;
                        $principal_repaid_derived = $principal_repaid_derived + $principal;
                        $amount = $amount - $principal;

                    } elseif ($amount < $principal && $principal > 0) {
                        $schedule->principal_repaid_derived = $schedule->principal_repaid_derived + $amount;
                        $principal_repaid_derived = $principal_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //interest
                    if ($amount >= $interest && $interest > 0) {
                        $schedule->interest_repaid_derived = $schedule->interest_repaid_derived + $interest;
                        $interest_repaid_derived = $interest_repaid_derived + $interest;
                        $amount = $amount - $interest;
                    } elseif ($amount < $interest && $interest > 0) {
                        $schedule->interest_repaid_derived = $schedule->interest_repaid_derived + $amount;
                        $interest_repaid_derived = $interest_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //penalties
                    if ($amount >= $penalties && $penalties > 0) {
                        $schedule->penalties_repaid_derived = $schedule->penalties_repaid_derived + $penalties;
                        $penalties_repaid_derived = $penalties_repaid_derived + $penalties;
                        $amount = $amount - $penalties;
                    } elseif ($amount < $penalties && $penalties > 0) {
                        $schedule->penalties_repaid_derived = $schedule->penalties_repaid_derived + $amount;
                        $penalties_repaid_derived = $penalties_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //fees
                    if ($amount >= $fees && $fees > 0) {
                        $schedule->fees_repaid_derived = $schedule->fees_repaid_derived + $fees;
                        $fees_repaid_derived = $fees_repaid_derived + $fees;
                        $amount = $amount - $fees;
                    } elseif ($amount < $fees && $fees > 0) {
                        $schedule->fees_repaid_derived = $schedule->fees_repaid_derived + $amount;
                        $fees_repaid_derived = $fees_repaid_derived + $amount;
                        $amount = 0;
                    }

                }
                if ($loan->loan_transaction_processing_strategy_id == 3) {

                    //interest
                    if ($amount >= $interest && $interest > 0) {
                        $schedule->interest_repaid_derived = $schedule->interest_repaid_derived + $interest;
                        $interest_repaid_derived = $interest_repaid_derived + $interest;
                        $amount = $amount - $interest;
                    } elseif ($amount < $interest && $interest > 0) {
                        $schedule->interest_repaid_derived = $schedule->interest_repaid_derived + $amount;
                        $interest_repaid_derived = $interest_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //principal
                    if ($amount >= $principal && $principal > 0) {
                        $schedule->principal_repaid_derived = $schedule->principal_repaid_derived + $principal;
                        $principal_repaid_derived = $principal_repaid_derived + $principal;
                        $amount = $amount - $principal;

                    } elseif ($amount < $principal && $principal > 0) {
                        $schedule->principal_repaid_derived = $schedule->principal_repaid_derived + $amount;
                        $principal_repaid_derived = $principal_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //penalties
                    if ($amount >= $penalties && $penalties > 0) {
                        $schedule->penalties_repaid_derived = $schedule->penalties_repaid_derived + $penalties;
                        $penalties_repaid_derived = $penalties_repaid_derived + $penalties;
                        $amount = $amount - $penalties;
                    } elseif ($amount < $penalties && $penalties > 0) {
                        $schedule->penalties_repaid_derived = $schedule->penalties_repaid_derived + $amount;
                        $penalties_repaid_derived = $penalties_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //fees
                    if ($amount >= $fees && $fees > 0) {
                        $schedule->fees_repaid_derived = $schedule->fees_repaid_derived + $fees;
                        $fees_repaid_derived = $fees_repaid_derived + $fees;
                        $amount = $amount - $fees;
                    } elseif ($amount < $fees && $fees > 0) {
                        $schedule->fees_repaid_derived = $schedule->fees_repaid_derived + $amount;
                        $fees_repaid_derived = $fees_repaid_derived + $amount;
                        $amount = 0;
                    }
                }
                if (($schedule->principal - $schedule->principal_written_off_derived - $schedule->principal_repaid_derived) + ($schedule->interest - $schedule->interest_written_off_derived - $schedule->interest_repaid_derived - $schedule->interest_waived_derived) + ($schedule->fees - $schedule->fees_written_off_derived - $schedule->fees_repaid_derived - $schedule->fees_waived_derived) + ($schedule->penalties - $schedule->penalties_written_off_derived - $schedule->penalties_repaid_derived - $schedule->penalties_waived_derived) <= 0) {
                    $schedule->paid_by_date = $transaction->submitted_on;
                }
                $schedule->total_due = ($schedule->principal - $schedule->principal_written_off_derived - $schedule->principal_repaid_derived) + ($schedule->interest - $schedule->interest_written_off_derived - $schedule->interest_repaid_derived - $schedule->interest_waived_derived) + ($schedule->fees - $schedule->fees_written_off_derived - $schedule->fees_repaid_derived - $schedule->fees_waived_derived) + ($schedule->penalties - $schedule->penalties_written_off_derived - $schedule->penalties_repaid_derived - $schedule->penalties_waived_derived);
                $schedule->balance = $schedule->total_due;
                $schedule->save();
                if ($amount <= 0) {
                    break;
                }
            }
            $transaction->principal_repaid_derived = $principal_repaid_derived;
            $transaction->interest_repaid_derived = $interest_repaid_derived;
            $transaction->fees_repaid_derived = $fees_repaid_derived;
            $transaction->penalties_repaid_derived = $penalties_repaid_derived;
            $transaction->save();
            if ($amount <= 0) {
                continue;
            }
        }

        //echo json_encode($transactions);
        $unchanged_transactions = [];
        foreach ($original_transactions as $key) {
            $unchanged_transactions[] = [
                $key->id,
                $key->loan_id,
                $key->payment_detail_id,
                $key->amount,
                $key->principal_repaid_derived,
                $key->interest_repaid_derived,
                $key->fees_repaid_derived,
                $key->penalties_repaid_derived,
                $key->submitted_on,
            ];
        }
        $changed_transactions = [];
        $count = 1;
        foreach ($transactions as $key) {
            $changed_transactions[] = [
                $key->id,
                $key->loan_id,
                $key->payment_detail_id,
                $key->amount,
                $key->principal_repaid_derived,
                $key->interest_repaid_derived,
                $key->fees_repaid_derived,
                $key->penalties_repaid_derived,
                $key->submitted_on,
            ];
            $count++;
        }
        $transactions_to_be_updated = compare_multi_dimensional_array($changed_transactions, $unchanged_transactions);
        foreach ($transactions_to_be_updated as $key => $value) {
            $transaction = $unchanged_transactions[$key];
            //check if accounting is enabled
            if ($loan->product->accounting_rule == "cash" || $loan->product->accounting_rule == "accrual_periodic" || $loan->product->accounting_rule == "accrual_upfront") {
                //reverse all journal entries linked to this transactions
                foreach (JournalEntry::where('transaction_number', 'L' . $transaction[0])->get() as $journal_entry) {
                    if ($journal_entry->debit > $journal_entry->credit) {
                        $journal_entry->credit = $journal_entry->debit;
                    } else {
                        $journal_entry->debit = $journal_entry->credit;
                    }
                    $journal_entry->reversed = 1;
                    $journal_entry->save();
                }
                //principal repaid
                if ($transaction[4] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->product->loan_portfolio_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[4];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();
                    //debit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->product->fund_source_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->debit = $transaction[3];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();
                }
                //interest repaid
                if ($transaction[5] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->product->income_from_interest_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[5];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();

                }
                //fees repaid
                if ($transaction[6] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->product->income_from_fees_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[6];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();

                }
                //penalties repaid
                if ($transaction[7] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->product->income_from_penalties_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[7];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();

                }
            }
        }
        //update balances
        Log::info('Got here to refresh');
        $loan->refresh();
        $loan->principal_disbursed_derived = $loan->schedules->sum('principal');
        $loan->principal_repaid_derived = $loan->schedules->sum('principal_repaid_derived');
        $loan->principal_written_off_derived = $loan->schedules->sum('principal_written_off_derived');
        $loan->principal_outstanding_derived = $loan->principal_disbursed_derived - $loan->principal_repaid_derived - $loan->principal_written_off_derived;
        $loan->interest_disbursed_derived = $loan->schedules->sum('interest');
        $loan->interest_repaid_derived = $loan->schedules->sum('interest_repaid_derived');
        $loan->interest_written_off_derived = $loan->schedules->sum('interest_written_off_derived');
        $loan->interest_waived_derived = $loan->schedules->sum('interest_waived_derived');
        $loan->interest_outstanding_derived = $loan->interest_disbursed_derived - $loan->interest_repaid_derived - $loan->interest_written_off_derived - $loan->interest_waived_derived;
        $loan->fees_disbursed_derived = $loan->schedules->sum('fees');
        $loan->fees_repaid_derived = $loan->schedules->sum('fees_repaid_derived');
        $loan->fees_written_off_derived = $loan->schedules->sum('fees_written_off_derived');
        $loan->fees_waived_derived = $loan->schedules->sum('fees_waived_derived');
        $loan->fees_outstanding_derived = $loan->fees_disbursed_derived - $loan->fees_repaid_derived - $loan->fees_written_off_derived - $loan->fees_waived_derived;
        $loan->penalties_disbursed_derived = $loan->schedules->sum('penalties');
        $loan->penalties_repaid_derived = $loan->schedules->sum('penalties_repaid_derived');
        $loan->penalties_written_off_derived = $loan->schedules->sum('penalties_written_off_derived');
        $loan->penalties_waived_derived = $loan->schedules->sum('penalties_waived_derived');
        $loan->penalties_outstanding_derived = $loan->penalties_disbursed_derived - $loan->penalties_repaid_derived - $loan->penalties_written_off_derived - $loan->penalties_waived_derived;
        $loan->total_disbursed_derived = $loan->principal_disbursed_derived + $loan->interest_disbursed_derived + $loan->fees_disbursed_derived + $loan->penalties_disbursed_derived;
        $loan->total_repaid_derived = $loan->principal_repaid_derived + $loan->interest_repaid_derived + $loan->fees_repaid_derived + $loan->penalties_repaid_derived;
        $loan->total_written_off_derived = $loan->principal_written_off_derived + $loan->interest_written_off_derived + $loan->fees_written_off_derived + $loan->penalties_written_off_derived;
        $loan->total_waived_derived = $loan->interest_waived_derived + $loan->fees_waived_derived + $loan->penalties_waived_derived;
        $loan->total_outstanding_derived = $loan->principal_outstanding_derived + $loan->interest_outstanding_derived + $loan->fees_outstanding_derived + $loan->penalties_outstanding_derived;
        if ($loan->total_outstanding_derived <= 0 & $loan->status === 'active') {
            $loan->status = 'closed';
        }
        $loan->save();
    }
}
