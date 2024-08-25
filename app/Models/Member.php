<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Features;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Member extends Model
{
    use LogsActivity, LogsActivity,Notifiable;

    protected $table = 'members';
    protected $fillable = ['created_by_id', 'branch_id', 'loan_officer_id', 'reference', 'account_number', 'gender', 'status', 'marital_status', 'country_id', 'title_id', 'profession_id', 'member_type_id', 'mobile', 'phone', 'email', 'external_id', 'dob', 'address', 'city', 'state', 'zip', 'employer', 'photo', 'notes', 'signature', 'created_date', 'joined_date', 'activation_date'];
    protected $appends = ['name', 'name_id', 'profile_photo_url', 'age'];
    protected $casts = [
        'english' => 'boolean',
        'eswatini' => 'boolean',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%");
                $query->orWhere('last_name', 'like', "%$search%");
                $query->orWhere('account_number', 'like', "%$search%");
                $query->orWhere('graded_tax_number', 'like', "%$search%");
                $query->orWhere('identification_number', 'like', "%$search%");
                $query->orWhere('contact_number', 'like', "%$search%");
                $query->orWhere('home_number', 'like', "%$search%");
                $query->orWhere('external_id', 'like', "%$search%");
                $query->orWhere('email', 'like', "%$search%");
                $query->orWhere('id', 'like', "%$search%");
            });
        });
        $query->when($filters['gender'] ?? null, function ($query, $search) {

            $query->where('gender', $search);
        });
    }

    public function getAgeAttribute()
    {
        return Carbon::now()->diffForHumans(Carbon::parse($this->dob), true, true);
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    public function getNameIDAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name . ' (#' . $this->id . ')';
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function loanOfficer()
    {
        return $this->belongsTo(User::class, 'loan_officer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function title()
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function type()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MemberCategory::class, 'member_category_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(MemberDesignation::class, 'member_designation_id');
    }

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(MemberBeneficiary::class, 'member_id', 'id');
    }

    public function other_loan(): HasOne
    {
        return $this->hasOne(OtherLoan::class, 'member_id', 'id')->latest();
    }

    public function identifications()
    {
        return $this->hasMany(MemberIdentification::class, 'member_id', 'id');
    }

    public function memberUsers()
    {
        return $this->hasMany(MemberUser::class, 'member_id', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'record_id', 'id')->where('category', 'members');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'record_id', 'id')->where('category', 'members');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'member_id', 'id');
    }

    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class, 'member_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->photo
            ? Storage::disk($this->profilePhotoDisk())->url($this->photo)
            : $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    protected function profilePhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('jetstream.profile_photo_disk', 'public');
    }

    public function updateProfilePhoto(UploadedFile $photo)
    {
        tap($this->profile_photo_path, function ($previous) use ($photo) {
            $this->forceFill([
                'photo' => $photo->storePublicly(
                    'profile-photos', ['disk' => $this->profilePhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {
        if (!Features::managesProfilePhotos()) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

        $this->forceFill([
            'photo' => null,
        ])->save();
    }
    public function routeNotificationForTwilio()
    {
        return $this->contact_number;
    }
}
