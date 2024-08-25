<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class LoanTransaction extends Model
{
    protected $fillable = [];
    public $table = "loan_transactions";
    protected $casts = [
        'balance' => 'double:2',
        'amount' => 'double:2',
        'credit' => 'double:2',
        'debit' => 'double:2',
        'reversed' => 'bool',
        'reversible' => 'bool',
    ];

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%');
                $query->orWhere('reference', 'like', "%$search%");
                $query->orWhereHas('loan', function (Builder $query) use ($search) {
                    $query->where('id', 'like', "%$search%");
                    $query->orWhere('external_id', 'like', "%$search%");
                });
                $query->orWhereHas('loan.member', function (Builder $query) use ($search) {
                    $query->where('id', 'like', "%$search%");
                    $query->orWhere('first_name', 'like', "%$search%");
                    $query->orWhere('last_name', 'like', "%$search%");
                });
                $query->orWhereHas('paymentDetail', function (Builder $query) use ($search) {
                    $query->where('receipt', 'like', "%$search%");
                    $query->orWhere('cheque_number', 'like', "%$search%");
                    $query->orWhere('account_number', 'like', "%$search%");
                    $query->orWhere('description', 'like', "%$search%");
                });
            });
        });
        $query->when($filters['payment_type_id'] ?? null, function (Builder $query, $filter) {
            $query->whereHas('paymentDetail', function (Builder $query) use ($filter) {
                $query->where('payment_type_id', $filter);
            });
        });
        $query->when($filters['member_id'] ?? null, function (Builder $query, $filter) {
            $query->whereHas('loan', function (Builder $query) use ($filter) {
                $query->where('member_id', $filter);
            });
        });
        $query->when($filters['currency_id'] ?? null, function (Builder $query, $filter) {
            $query->whereHas('loan', function (Builder $query) use ($filter) {
                $query->where('currency_id', $filter);
            });
        });
        $query->when($filters['loan_product_id'] ?? null, function (Builder $query, $filter) {
            $query->whereHas('loan', function (Builder $query) use ($filter) {
                $query->where('loan_product_id', $filter);
            });
        });
        $query->when($filters['loan_officer_id'] ?? null, function (Builder $query, $filter) {
            $query->whereHas('loan', function (Builder $query) use ($filter) {
                $query->where('loan_officer_id', $filter);
            });
        });
        $query->when($filters['branch_id'] ?? null, function (Builder $query, $filter) {
            $query->whereHas('loan', function (Builder $query) use ($filter) {
                $query->where('branch_id', $filter);
            });
        });
    }

    public function paymentDetail()
    {
        return $this->belongsTo(PaymentDetail::class, 'payment_detail_id');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function type()
    {
        return $this->belongsTo(LoanTransactionType::class, 'loan_transaction_type_id');
    }
}
