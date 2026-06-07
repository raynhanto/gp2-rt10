<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Role hierarchy (higher index = more power)
    public const ROLES = ['warga', 'sekretaris', 'bendahara', 'admin', 'super_admin'];

    // Roles that have any kind of admin panel access
    public const ADMIN_ROLES = ['admin', 'super_admin', 'bendahara', 'sekretaris'];

    // Roles that have financial access (keuangan module)
    public const KEUANGAN_ROLES = ['admin', 'super_admin', 'bendahara'];

    // Only these roles can manage users, roles, and system settings
    public const SUPER_ROLES = ['admin', 'super_admin'];

    protected $fillable = [
        'google_id', 'email', 'nama', 'no_wa',
        'role', 'profil_lengkap', 'avatar_url',
    ];

    protected $hidden = ['google_id', 'remember_token'];

    protected function casts(): array
    {
        return [
            'profil_lengkap' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, self::ADMIN_ROLES, true);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, self::SUPER_ROLES, true);
    }

    public function isBendahara(): bool
    {
        return in_array($this->role, self::KEUANGAN_ROLES, true);
    }

    public function isSekretaris(): bool
    {
        return $this->role === 'sekretaris' || $this->isSuperAdmin();
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role, $roles, true);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'Admin',
            'bendahara'   => 'Bendahara',
            'sekretaris'  => 'Sekretaris',
            default       => 'Warga',
        };
    }

    public function units(): HasMany
    {
        return $this->hasMany(UnitRumah::class)
            ->orderByDesc('is_primary')
            ->orderBy('blok')
            ->orderBy('nomor');
    }

    public static function upsertFromGoogle(array $googleData): self
    {
        $user = self::where('google_id', $googleData['google_id'])->first();

        if ($user) {
            $user->update(['avatar_url' => $googleData['avatar_url']]);
            return $user->fresh();
        }

        return self::create([
            'google_id'  => $googleData['google_id'],
            'email'      => $googleData['email'],
            'avatar_url' => $googleData['avatar_url'],
        ]);
    }
}
