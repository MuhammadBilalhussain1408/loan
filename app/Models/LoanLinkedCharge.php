<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LoanLinkedCharge extends Model
{
    use LogsActivity;
    protected $fillable = [];
    public $table = "loan_linked_charges";
    protected $casts = [
        'waived' => 'boolean',
        'is_penalty' => 'boolean',
        'is_paid' => 'boolean',
    ];

    public function charge()
    {
        return $this->hasOne(LoanCharge::class, 'id', 'loan_charge_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function transaction()
    {
        return $this->hasOne(LoanTransaction::class, 'id', 'loan_transaction_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
