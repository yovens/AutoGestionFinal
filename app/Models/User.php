<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    public const ADMIN  = 'ADMIN';
    public const CLIENT = 'CLIENT';

    public const ACTIVE  = 'ACTIVE';
    public const BLOCKED = 'BLOCKED';

    protected $fillable = [
        'nom', 'email', 'password', 'role', 'status', 'telephone',
        'profile_image', 'bio', 'google_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function loans()
    {
        return $this->hasMany(LoanCart::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::BLOCKED;
    }

    /** Ki paj yon itilizatè dwe ateri lè l fin konekte. */
    public function homeRoute(): string
    {
        return $this->isAdmin() ? route('admin.dashboard') : route('client.dashboard');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ADMIN => 'Administrateur',
            default     => 'Client',
        };
    }

    /**
     * Foto pwofil. Kliyan yo te konn sove nan `profile_image`, admin nan `profile_photo_path`;
     * nou li toude pou pa pèdi foto ki deja la.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $path = $this->profile_image ?: $this->profile_photo_path;

        return $path ? Storage::url($path) : null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->nom)) ?: [];
        $initials = collect($parts)->filter()->take(2)->map(fn ($p) => Str::upper(Str::substr($p, 0, 1)))->implode('');

        return $initials ?: 'AG';
    }

    public function getFirstNameAttribute(): string
    {
        return Str::before(trim((string) $this->nom), ' ') ?: 'Client';
    }
}
