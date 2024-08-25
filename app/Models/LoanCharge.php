<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LoanCharge extends Model
{
    use LogsActivity;

    protected $fillable = [];
    public $table = "loan_charges";
    protected $casts = [
        'active' => 'boolean',
        'is_penalty' => 'boolean',
        'allow_override' => 'boolean',
        'schedule' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(LoanChargeType::class, 'loan_charge_type_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(LoanChargeOption::class, 'loan_charge_option_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
