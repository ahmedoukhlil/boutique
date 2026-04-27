<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Permission;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'actif',
    ];

    const ROLES = [
        'admin'        => 'Administrateur',
        'gestionnaire' => 'Gestionnaire',
        'caissier'     => 'Caissier',
    ];

    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isGestionnaire(): bool { return in_array($this->role, ['admin', 'gestionnaire']); }
    public function getRoleLibelleAttribute(): string { return self::ROLES[$this->role] ?? ucfirst($this->role); }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, Permission::pourRole($this->role));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
        ];
    }
}
