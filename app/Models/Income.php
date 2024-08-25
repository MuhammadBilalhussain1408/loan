<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Income extends Model
{
    use LogsActivity, HasFactory;
    protected $table = 'income';
    protected $fillable = [];
    protected $casts = [
        'selected_days' => 'array',
        'recurring' => 'boolean'
    ];
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }
    public function assetChart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'asset_chart_of_account_id');
    }

    public function incomeChart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'income_chart_of_account_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function type()
    {
        return $this->hasOne(IncomeType::class, 'id', 'income_type_id');
    }
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

}
