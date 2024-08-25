<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class CommunicationCampaign extends Model
{
    use LogsActivity, HasFactory;

    protected $fillable = [];
    public $table = "communication_campaigns";
    protected $casts = [
        'active' => 'boolean',
        'selected_days' => 'array',

    ];

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function communicationCampaignBusinessRule(): BelongsTo
    {
        return $this->belongsTo(CommunicationCampaignBusinessRule::class, 'communication_campaign_business_rule_id');
    }

    public function communicationAttachmentType(): BelongsTo
    {
        return $this->belongsTo(CommunicationCampaignAttachmentType::class, 'communication_campaign_attachment_type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

}
