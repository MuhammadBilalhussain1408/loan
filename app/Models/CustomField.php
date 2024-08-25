<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomField extends Model
{
    use LogsActivity;

    protected $table = "custom_fields";
    protected $fillable = [];
    protected $casts = [
        'active' => 'boolean',
        'include_in_report' => 'boolean',
        'required' => 'boolean',
        'options' => 'array',
        'rules' => 'array',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function meta()
    {
        return $this->hasMany(CustomFieldMeta::class, 'custom_field_id', 'id');
    }
}
