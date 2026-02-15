<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Collection;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'curp',
        'name',
        'email',
        'password',
        'institution_id',
        'priority_area_id',
        'sub_area_id',
        'email_verification_code',
        'email_verification_code_expires_at',
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
            'email_verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function priorityArea(): BelongsTo
    {
        return $this->belongsTo(PriorityArea::class);
    }

    public function subArea(): BelongsTo
    {
        return $this->belongsTo(SubArea::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user')
            ->withPivot('status', 'registration_date')
            ->withTimestamps();
    }

    public function getAllowedViews($module): Collection
    {
        return $this->getAllPermissions()->where('module_key', $module)->pluck('arg');
    }

    public function getRolesArray(): Collection
    {
        return $this->roles()->get()->mapWithKeys(function ($role) {
            return [$role['name'] => true];
        });
    }

    public function getPermissionArray(): Collection
    {
        return $this->getAllPermissions()->mapWithKeys(function ($permission) {
            return [$permission['name'] => true];
        });
    }

    /**
     * Get the primary role of the user
     */
    public function getPrimaryRole(): ?string
    {
        return $this->roles()->first()?->name;
    }

    /**
     * Get the layout name based on user role
     */
    public function getLayoutName(): string
    {
        $role = $this->getPrimaryRole();
        
        return match($role) {
            'Super Admin' => 'LayoutAuthenticated',
            'Admin' => 'AdminLayout',
            'Evaluador' => 'EvaluatorLayout',
            'Docente' => 'TeacherLayout',
            default => 'TeacherLayout',
        };
    }

    /**
     * Check if user has specific permission
     */
    /**
     * Check if user has specific permission
     */
    public function canPermission(string $permission): bool
    {
        return $this->hasPermissionTo($permission);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeBuscarGlobal($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhereHas('roles', function ($qRole) use ($search) {
                  $qRole->where('name', 'LIKE', "%{$search}%");
              });
        });
    }

    public function scopeOrdenado($query, $sortField = 'id', $sortDirection = 'desc')
    {
        return $query->orderBy($sortField, $sortDirection);
    }
}
