<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LoanApplicationLinkedChecklistItem extends Model
{
    use LogsActivity, HasFactory;

    protected $fillable = [
        'loan_application_checklist_item_id',
        'loan_application_id',
        'completed',
        'status',
        'completed_at',
        'completed_by_id',
        'name',
    ];
    protected $casts = [
        'completed' => 'boolean',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(LoanApplicationChecklistItem::class, 'loan_application_checklist_item_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
