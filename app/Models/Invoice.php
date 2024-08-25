<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'decimal:2',
        'xrate' => 'decimal:2',
        'shortfall' => 'decimal:2',
        'balance' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'co_payer_amount' => 'decimal:2',
        'cash_balance' => 'decimal:2',
        'co_payer_balance' => 'decimal:2',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%');
                $query->orWhere('reference', 'like', '%' . $search . '%')
                    ->orWhereHas('patient', function ($query) use ($search){
                        $query->where('id', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('mobile', 'like', '%' . $search . '%')
                            ->orWhere('first_name', 'like', '%' . $search . '%')
                            ->orWhere('external_id', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('middle_name', 'like', '%' . $search . '%');
                    })->orWhereHas('coPayer', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        });
        $query->when($filters['status'] ?? null, function ($query, $search) {
            $query->where('status', $search);
        });
        $query->when($filters['currency_id'] ?? null, function ($query, $search) {
            $query->where('currency_id', $search);
        });
        $query->when($filters['tenant_id'] ?? null, function ($query, $search) {
            $query->where('tenant_id', $search);
        });
        $query->when($filters['co_payer_id'] ?? null, function ($query, $search) {
            $query->where('co_payer_id', $search);
        });
        $query->when($filters['sponsor'] ?? null, function ($query, $search) {
            $query->where('sponsor', $search);
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
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }
    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class, 'tax_rate_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function patientCoPayer()
    {
        return $this->belongsTo(PatientCoPayer::class, 'patient_co_payer_id', 'id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function coPayer()
    {
        return $this->belongsTo(CoPayer::class, 'co_payer_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id', 'id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }
    public function sale()
    {
        return $this->belongsTo(InventoryProductSale::class, 'inventory_product_sale_id', 'id');
    }
    public function claim()
    {
        return $this->hasOne(Claim::class, 'invoice_id', 'id');
    }
}
