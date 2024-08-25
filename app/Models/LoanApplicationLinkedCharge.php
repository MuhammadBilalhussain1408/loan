<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanApplicationLinkedCharge extends Model
{
    protected $fillable = [];
    public $table = "loan_application_linked_charges";
    protected $casts = [

    ];

    public function charge(): BelongsTo
    {
        return $this->belongsTo(LoanCharge::class, 'loan_charge_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

}
