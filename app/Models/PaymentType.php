<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $table = "payment_types";
    protected $fillable = [];
    protected $casts = [
        'is_cash' => 'boolean',
        'is_online' => 'boolean',
        'is_system' => 'boolean',
        'active' => 'boolean',
        'options' => 'array',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }
}
