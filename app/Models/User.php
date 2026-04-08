<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
        'city',
        'state',
        'avatar_url',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    // Relationships
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'estate_manager_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    public function leadsAssigned(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function leadsCreated(): HasMany
    {
        return $this->hasMany(Lead::class, 'marketer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function ordersManaged(): HasMany
    {
        return $this->hasMany(Order::class, 'manager_id');
    }

    public function documentsUploaded(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function messagesReceived(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function messagesSent(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function propertyAssignments(): HasMany
    {
        return $this->hasMany(PropertyAssignment::class, 'realtor_id');
    }

    public function clientVisits(): HasMany
    {
        return $this->hasMany(ClientVisit::class, 'realtor_id');
    }

    public function projectUpdates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class, 'field_agent_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
