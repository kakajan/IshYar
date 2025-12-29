<?php
namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, HasUuids, SoftDeletes;

    /**
     * The guard name for permission checking.
     */
    protected string $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'timezone',
        'locale',
        'organization_id',
        'department_id',
        'position_id',
        'manager_id',
        'is_active',
        'last_login_at',
        'preferences',
        'phone_verified_at',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'last_login_at'           => 'datetime',
            'is_active'               => 'boolean',
            'preferences'             => 'array',
            'notification_preferences' => 'array',
            'two_factor_confirmed_at' => 'datetime',
            'phone_verified_at'       => 'datetime',
        ];
    }

    /**
     * Get the identifier for JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return custom claims for JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'email' => $this->email,
            'name'  => $this->name,
        ];
    }

    /**
     * Determine if user can access Filament admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['super-admin', 'admin', 'manager']);
    }

    /**
     * Get user's organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get user's department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get user's position.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get user's manager.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get users that report to this user.
     */
    public function directReports()
    {
        return $this->hasMany(User::class, 'manager_id');
    }
}
