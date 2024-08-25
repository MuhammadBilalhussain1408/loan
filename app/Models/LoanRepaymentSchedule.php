<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRepaymentSchedule extends Model
{
    protected $fillable = ['principal_repaid_derived', 'fees_repaid_derived', 'interest_repaid_derived', 'penalties_repaid_derived'];
    public $table = "loan_repayment_schedules";
    protected $casts = [
        'principal' => 'decimal:2',
        'principal_repaid_derived' => 'decimal:2',
        'principal_written_off_derived' => 'decimal:2',
        'interest' => 'decimal:2',
        'interest_repaid_derived' => 'decimal:2',
        'interest_written_off_derived' => 'decimal:2',
        'interest_waived_derived' => 'decimal:2',
        'fees' => 'decimal:2',
        'fees_repaid_derived' => 'decimal:2',
        'fees_written_off_derived' => 'decimal:2',
        'fees_waived_derived' => 'decimal:2',
        'penalties' => 'decimal:2',
        'penalties_repaid_derived' => 'decimal:2',
        'penalties_written_off_derived' => 'decimal:2',
        'penalties_waived_derived' => 'decimal:2',
        'total_due' => 'decimal:2',
        'total' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'late_payment' => 'boolean',
    ];

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }
}
