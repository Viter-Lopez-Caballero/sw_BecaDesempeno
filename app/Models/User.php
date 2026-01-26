<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Collection;

class User extends Authenticatable
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
        'name',
        'email',
        'password',
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
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
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
            'Evaluador' => 'EvaluadorLayout',
            'Docente' => 'DocenteLayout',
            default => 'DocenteLayout',
        };
    }

    /**
     * Check if user has specific permission
     */
    public function canPermission(string $permission): bool
    {
        return $this->hasPermissionTo($permission);
    }
}
