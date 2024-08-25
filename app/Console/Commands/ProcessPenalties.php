<?php

namespace App\Console\Commands;


use App\Events\TransactionUpdated;
use App\Models\Loan;
use App\Models\LoanChargeType;
use App\Models\LoanLinkedCharge;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ProcessPenalties extends Command
{


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'loans:process-penalties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply penalties to all due loans';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $due_date = Carbon::today()->format("Y-m-d");
        //overdue installment fee
        $data = DB::table("loan_product_linked_charges")
            ->join("loan_charges", "loan_charges.id", "loan_product_linked_charges.loan_charge_id")
            ->join("loan_products", "loan_products.id", "loan_product_linked_charges.loan_product_id")
            ->join("loans", "loans.loan_product_id", "loan_products.id")
            ->join("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
            ->where("loan_charges.loan_charge_type_id", LoanChargeType::where('name', 'Overdue Installment Fee')->first()->id)
            ->where("loan_charges.is_penalty", 1)
            ->where("loan_repayment_schedules.due_date", $due_date)
            ->where("loan_repayment_schedules.total_due", '>', 0)
            ->selectRaw("loan_charges.id loan_charge_id,loan_charges.loan_charge_type_id,loan_charges.loan_charge_option_id,loan_charges.name charge_name,loan_charges.amount,loan_schedules.id loan_repayment_schedule_id,loan_schedules.due_date,loans.id loan_id,loans.branch_id,loans.principal,(loan_schedules.principal-loan_schedules.principal_repaid_derived-loan_schedules.principal_written_off_derived) principal_due,(loan_schedules.interest-loan_schedules.interest_repaid_derived-loan_schedules.interest_waived_derived-loan_schedules.interest_written_off_derived) interest_due,loans.decimals")
            ->get();
        foreach ($data as $key) {
            $loan = Loan::with('schedules')->find($key->loan_id);
            $loanLinkedCharge = new LoanLinkedCharge();
            $loanLinkedCharge->loan_id = $loan->id;
            $loanLinkedCharge->name = $key->charge_name;
            $loanLinkedCharge->loan_charge_id = $key->loan_charge_id;
            $loanLinkedCharge->amount = $key->amount;
            $loanLinkedCharge->loan_charge_type_id = $key->loan_charge_type_id;
            $loanLinkedCharge->loan_charge_option_id = $key->loan_charge_option_id;
            $loanLinkedCharge->is_penalty = 1;
            $loanLinkedCharge->save();
            //find schedule to apply this charge
            $schedule = $loan->schedules->where('due_date', '=', $key->due_date)->first();
            //calculate the amount
            if ($loanLinkedCharge->charge->option->name === 'Flat') {
                $amount = $loanLinkedCharge->amount;
            }
            if ($loanLinkedCharge->charge->option->name === 'Principal due on installment') {
                $amount = round(($loanLinkedCharge->amount * ($schedule->principal - $schedule->principal_repaid_derived - $schedule->principal_written_off_derived) / 100), $loan->decimals);
            }
            if ($loanLinkedCharge->charge->option->name === 'Principal + Interest due on installment') {
                $amount = round(($loanLinkedCharge->amount * (($schedule->interest - $schedule->interest_repaid_derived - $schedule->interest_waived_derived - $schedule->interest_written_off_derived) + ($schedule->principal - $schedule->principal_repaid_derived - $schedule->principal_written_off_derived)) / 100), $loan->decimals);
            }
            if ($loanLinkedCharge->charge->option->name === 'Interest due on installment') {
                $amount = round(($loanLinkedCharge->amount * ($schedule->interest - $schedule->interest_repaid_derived - $schedule->interest_waived_derived - $schedule->interest_written_off_derived) / 100), $loan->decimals);
            }
            if ($loanLinkedCharge->charge->option->name === 'Total Outstanding Loan Principal') {
                $amount = round(($loanLinkedCharge->amount * ($loan->schedules->sum('principal') - $loan->schedules->sum('principal_repaid_derived') - $loan->schedules->sum('principal_written_off_derived')) / 100), $loan->decimals);
            }
            if ($loanLinkedCharge->charge->option->name === 'Percentage of Original Loan Principal per Installment') {
                $amount = round(($loanLinkedCharge->amount * $loan->principal / 100), $loan->decimals);
            }
            if ($loanLinkedCharge->charge->option->name === 'Original Loan Principal') {
                $amount = round(($loanLinkedCharge->amount * $loan->principal / 100), $loan->decimals);
            }
            $schedule->penalties = $schedule->penalties + $amount;
            $schedule->save();
            $loanLinkedCharge->calculated_amount = $amount;
            $loanLinkedCharge->due_date = $schedule->due_date;
            //create transaction
            $transaction = new LoanTransaction();
            $transaction->loan_id = $loan->id;
            // $transaction->created_by_id=Auth::id();
            $transaction->name = 'Penalty Applied';
            $transaction->loan_transaction_type_id =LoanTransactionType::where('name','Apply Charges')->first()->id;
            $transaction->submitted_on = $schedule->due_date;
            $transaction->created_on = date("Y-m-d");
            $transaction->amount = $loanLinkedCharge->calculated_amount;
            $transaction->due_date = $schedule->due_date;
            $transaction->debit = $loanLinkedCharge->calculated_amount;
            $transaction->reversible = 1;
            $transaction->save();
            $loanLinkedCharge->loan_transaction_id = $transaction->id;
            $loanLinkedCharge->save();
            //add linked loan charge
            //fire transaction updated event
            event(new TransactionUpdated($loan));
        }
        $this->info("Penalties processed successfully");
        return CommandAlias::SUCCESS;
    }


}
