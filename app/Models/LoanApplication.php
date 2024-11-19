<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class LoanApplication extends Model
{
    use LogsActivity;

    protected $fillable = [];
    public $table = "loan_applications";

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }


    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MemberCategory::class, 'member_category_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(MemberDesignation::class, 'member_designation_id');
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

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(LoanApplicationChecklist::class, 'loan_application_checklist_id');
    }

    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(LoanApplicationLinkedApprovalStage::class, 'current_loan_application_linked_approval_stage_id');
    }

    public function nextStage(): BelongsTo
    {
        return $this->belongsTo(LoanApplicationLinkedApprovalStage::class, 'next_loan_application_linked_approval_stage_id');
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class, 'loan_application_id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(LoanApplicationLinkedCharge::class, 'loan_application_id', 'id');
    }
    public function declarations(): HasMany
    {
        return $this->hasMany(LoanApplicationDeclaration::class, 'loan_id', 'id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(LoanApplicationLinkedChecklistItem::class, 'loan_application_id', 'id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(LoanApplicationLinkedApprovalStage::class, 'loan_application_id', 'id')->orderBy('created_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'record_id', 'id')->where('category', 'loan_application')->orderBy('created_at', 'desc');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'record_id', 'id')->where('category', 'loan_application')->orderBy('created_at', 'desc');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty();
    }
}
