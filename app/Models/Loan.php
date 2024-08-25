<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Loan extends Model
{
    use LogsActivity;

    protected $fillable = [];
    public $table = "loans";
    protected $casts = [
        'enable_balloon_payments' => 'boolean',
        'allow_schedule_adjustments' => 'boolean',
        'allow_custom_grace_period' => 'boolean',
        'allow_topup' => 'boolean',
        'interest_recalculation' => 'boolean',
        'include_in_loan_cycle' => 'boolean',
        'lock_guarantee_funds' => 'boolean',
        'auto_allocate_overpayments' => 'boolean',
        'allow_additional_charges' => 'boolean',
        'auto_disburse' => 'boolean',
        'deduct_interest_from_principal' => 'boolean',
        'principal' => 'decimal:2',
        'applied_amount' => 'decimal:4',
        'interest_rate' => 'decimal:2',
        'disbursement_charges' => 'decimal:2',
        'principal_disbursed_derived' => 'decimal:2',
        'principal_repaid_derived' => 'decimal:2',
        'principal_written_off_derived' => 'decimal:2',
        'principal_outstanding_derived' => 'decimal:2',
        'interest_disbursed_derived' => 'decimal:2',
        'interest_repaid_derived' => 'decimal:2',
        'interest_written_off_derived' => 'decimal:2',
        'interest_waived_derived' => 'decimal:2',
        'interest_outstanding_derived' => 'decimal:2',
        'fees_disbursed_derived' => 'decimal:2',
        'fees_repaid_derived' => 'decimal:2',
        'fees_written_off_derived' => 'decimal:2',
        'fees_waived_derived' => 'decimal:2',
        'fees_outstanding_derived' => 'decimal:2',
        'penalties_disbursed_derived' => 'decimal:2',
        'penalties_repaid_derived' => 'decimal:2',
        'penalties_written_off_derived' => 'decimal:2',
        'penalties_waived_derived' => 'decimal:2',
        'penalties_outstanding_derived' => 'decimal:2',
        'total_disbursed_derived' => 'decimal:2',
        'total_repaid_derived' => 'decimal:2',
        'total_written_off_derived' => 'decimal:2',
        'total_waived_derived' => 'decimal:2',
        'total_outstanding_derived' => 'decimal:2',
    ];

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%');
                $query->orWhere('account_number', 'like', "%$search%");
                $query->orWhere('external_id', 'like', "%$search%");
                $query->orWhereHas('product', function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                    $query->orWhere('short_name', 'like', "%$search%");
                });
                $query->orWhereHas('member', function (Builder $query) use ($search) {
                    $query->where('first_name', 'like', "%$search%");
                    $query->orWhere('last_name', 'like', "%$search%");
                    $query->orWhere('account_number', 'like', "%$search%");
                    $query->orWhere('mobile', 'like', "%$search%");
                    $query->orWhere('external_id', 'like', "%$search%");
                    $query->orWhere('email', 'like', "%$search%");
                    $query->orWhere('id', 'like', "%$search%");
                });
            });
        });
        $query->when($filters['member_id'] ?? null, function (Builder $query, $filter) {
            $query->where('member_id', $filter);
        });
        $query->when($filters['fund_id'] ?? null, function (Builder $query, $filter) {
            $query->where('fund_id', $filter);
        });
        $query->when($filters['loan_product_id'] ?? null, function (Builder $query, $filter) {
            $query->where('loan_product_id', $filter);
        });
        $query->when($filters['currency_id'] ?? null, function (Builder $query, $filter) {
            $query->where('currency_id', $filter);
        });
        $query->when($filters['branch_id'] ?? null, function (Builder $query, $filter) {
            $query->where('branch_id', $filter);
        });
        $query->when($filters['loan_officer_id'] ?? null, function (Builder $query, $filter) {
            $query->where('loan_officer_id', $filter);
        });
        $query->when($filters['loan_provisioning_id'] ?? null, function (Builder $query, $filter) {
            $query->where('loan_provisioning_id', $filter);
        });
        $query->when($filters['loan_purpose_id'] ?? null, function (Builder $query, $filter) {
            $query->where('loan_purpose_id', $filter);
        });
        $query->when($filters['loan_disbursement_channel_id'] ?? null, function (Builder $query, $filter) {
            $query->where('loan_disbursement_channel_id', $filter);
        });
        $query->when($filters['status'] ?? null, function (Builder $query, $filter) {
            $query->where('status', $filter);
        });
    }

    public function charges(): HasMany
    {
        return $this->hasMany(LoanLinkedCharge::class, 'loan_id', 'id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MemberCategory::class, 'member_category_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(MemberDesignation::class, 'member_designation_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function loanOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'loan_officer_id');
    }

    public function purpose(): BelongsTo
    {
        return $this->belongsTo(LoanPurpose::class, 'loan_purpose_id');
    }

    public function loanProvisioning(): BelongsTo
    {
        return $this->belongsTo(LoanProvisioning::class, 'loan_provisioning_id');
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }

    public function transactionProcessingStrategy(): BelongsTo
    {
        return $this->belongsTo(LoanTransactionProcessingStrategy::class, 'loan_transaction_processing_strategy_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function disbursedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by_user_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function withdrawnBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'withdrawn_by_user_id');
    }

    public function rescheduledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rescheduled_by_user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(LoanFile::class, 'loan_id', 'id');
    }


    public function notes(): HasMany
    {
        return $this->hasMany(LoanNote::class, 'loan_id', 'id')->orderBy('created_at', 'desc');
    }



    public function schedules(): HasMany
    {
        return $this->hasMany(LoanRepaymentSchedule::class, 'loan_id', 'id')->orderBy('due_date', 'asc');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class, 'loan_id', 'id')->orderBy('submitted_on', 'asc')->orderBy('id', 'asc');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty();
    }
}
