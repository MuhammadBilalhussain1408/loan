<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'co_payer_amount' => 'decimal:2',
    ];
    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class, 'tax_rate_id', 'id');
    }
    public function tariff()
    {
        return $this->belongsTo(Tariff::class, 'tariff_id', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
