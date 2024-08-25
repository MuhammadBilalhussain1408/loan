<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $casts = [
        'all_day' => 'boolean',
        'reminder' => 'boolean',
        'missed' => 'boolean',
    ];


    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('chartOfAccount', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        });
        $query->when($filters['branch_id'] ?? null, function ($query, $branch_id) {
            $query->where('branch_id', $branch_id);
        });
        $query->when($filters['currency_id'] ?? null, function ($query, $currency_id) {
            $query->where('currency_id', $currency_id);
        });
        $query->when($filters['chart_of_account_id'] ?? null, function ($query, $chart_of_account_id) {
            $query->where('chart_of_account_id', $chart_of_account_id);
        });
        $query->when($filters['date_range'] ?? null, function ($query, $search) {
            $date = explode(' to ', $search);
            if (!empty($date[1])) {
                $query->whereBetween('date', [$date[0], $date[1]]);
            }
        });
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $financialPeriod = FinancialPeriod::where('closed', 0)->first();
            if ($financialPeriod) {
                $model->financial_period_id = $financialPeriod->id ?? '';
            }
        });

    }


}
