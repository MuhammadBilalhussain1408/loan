<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_id',
        'patient_id',
        'payment_detail_id',
        'chart_of_account_debit_id',
        'chart_of_account_credit_id',
        'branch_id',
        'user_id',
        'currency_id',
        'invoice_item_id',
        'trans_id',
        'receipt',
        'invoice_id',
        'date',
        'amount',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%');
                $query->orWhere('receipt', 'like', '%' . $search . '%');
                $query->orWhere('trans_id', 'like', '%' . $search . '%');
                $query->orWhere('description', 'like', '%' . $search . '%');
                $query->orWhereHas('patient', function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%')
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('external_id', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('middle_name', 'like', '%' . $search . '%');
                });
                $query->orWhereHas('coPayer', function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%');
                });
            });
        });
        $query->when($filters['currency_id'] ?? null, function ($query, $search) {
            $query->where('currency_id', $search);
        });
        $query->when($filters['co_payer_id'] ?? null, function ($query, $search) {
            $query->where('co_payer_id', $search);
        });
        $query->when($filters['paid_by'] ?? null, function ($query, $search) {
            $query->where('paid_by', $search);
        });
        $query->when($filters['doctor_id'] ?? null, function ($query, $search) {
            $query->where('doctor_id', $search);
        });
        $query->when($filters['patient_id'] ?? null, function ($query, $search) {
            $query->where('patient_id', $search);
        });
        $query->when($filters['date_range'] ?? null, function ($query, $search) {
            $date = explode(' to ', $search);
            if (!empty($date[1])) {
                $query->whereBetween('date', [$date[0], $date[1]]);
            }
        });
    }

    public function paymentDetail()
    {
        return $this->belongsTo(PaymentDetail::class, 'payment_detail_id', 'id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id', 'id');
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function coPayer()
    {
        return $this->belongsTo(CoPayer::class, 'co_payer_id', 'id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }
}
