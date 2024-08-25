<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCreditCheck extends Model
{
    protected $fillable = [];
    public $table = "loan_credit_checks";
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }
}
