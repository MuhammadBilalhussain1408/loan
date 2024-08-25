<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanTransactionProcessingStrategy extends Model
{
    protected $fillable = [];
    public $table = "loan_transaction_processing_strategies";
    public $timestamps = false;
}
