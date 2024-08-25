<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LoanProduct extends Model
{
    use LogsActivity;

    protected $fillable = [];
    public $table = "loan_products";
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
        'require_linked_savings_account' => 'boolean',
        'npa_suspend_accrued_income' => 'boolean',
        'disallow_interest_rate_adjustment' => 'boolean',
        'deduct_interest_from_principal' => 'boolean',
        'exclude_weekends' => 'boolean',
        'exclude_holidays' => 'boolean',
        'active' => 'boolean',
        'minimum_principal' => 'decimal:2',
        'default_principal' => 'decimal:2',
        'maximum_principal' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'minimum_interest_rate' => 'decimal:2',
        'default_interest_rate' => 'decimal:2',
        'maximum_interest_rate' => 'decimal:2',

    ];

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function charges(): HasMany
    {
        return $this->hasMany(LoanProductLinkedCharge::class, 'loan_product_id', 'id');
    }

    public function creditChecks(): HasMany
    {
        return $this->hasMany(LoanProductLinkedCreditCheck::class, 'loan_product_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }

    public function processingStrategy(): BelongsTo
    {
        return $this->belongsTo(LoanTransactionProcessingStrategy::class, 'loan_transaction_processing_strategy_id');
    }

    public function disbursementChannel(): BelongsTo
    {
        return $this->belongsTo(LoanDisbursementChannel::class, 'loan_disbursement_channel_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
