<?php

namespace App\Imports;


use App\Events\TransactionUpdated;

use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\PaymentDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RepaymentsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, ShouldQueue
{
    use RemembersRowNumber, RegistersEventListeners;

    public  User $user;
    public array $results = [];
    /** @var Worksheet */
    protected Worksheet $worksheet;

    public function __construct($user = null)
    {
        $this->user = $user;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function columnFormats(): array
    {
        return [
            'date' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }

    public function collection(Collection $rows): void
    {

        foreach ($rows as $row) {
            $loanID = $row['loan_id'];
            $amount = $row['amount'];
            $date = $row['date'];
            $paymentMethodID = $row['payment_method'];
            $receipt = $row['receipt'];
            $loan = Loan::with('loan_product')->find($loanID);
            //payment details
            $paymentDetail = new PaymentDetail();
            $paymentDetail->created_by_id = Auth::id();
            $paymentDetail->payment_type_id = $paymentMethodID;
            $paymentDetail->transaction_type = 'loan_transaction';
            $paymentDetail->receipt = $receipt;
            $paymentDetail->save();
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = $this->user->id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->payment_detail_id = $paymentDetail->id;
            $loan_transaction->name = 'Repayment';
            $loan_transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Repayment')->first()->id;
            $loan_transaction->submitted_on = $date;
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->amount = $amount;
            $loan_transaction->credit = $amount;
            $loan_transaction->save();
            //fire transaction updated event
            event(new TransactionUpdated($loan));
        }
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->worksheet = $event->getSheet();

            },
        ];
    }

}
