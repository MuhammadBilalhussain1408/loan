<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MemberDesignation extends Model
{
    use LogsActivity, HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];
    protected $appends = [
        'total_members'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    protected function totalMembers(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => Member::where('member_designation_id', $this->id)->count(),
            set: fn($value) => $value,
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
