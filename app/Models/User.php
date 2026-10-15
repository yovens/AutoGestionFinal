<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    public const SUPER_ADMIN = 'SUPER_ADMIN';
    public const ADMIN       = 'ADMIN';
    public const SECRETARY   = 'SECRETAIRE';
    public const CLIENT      = 'CLIENT';

    /** Wòl anplwaye ki ka travay nan comptoir la. */
    public const STAFF = [self::SUPER_ADMIN, self::ADMIN, self::SECRETARY];

    /** Wòl ki gen aksè ak administrasyon an. */
    public const ADMINS = [self::SUPER_ADMIN, self::ADMIN];

    public const ROLE_LABELS = [
        self::CLIENT      => 'Client',
        self::SECRETARY   => 'Secrétaire',
        self::ADMIN       => 'Administrateur',
        self::SUPER_ADMIN => 'Super administrateur',
    ];

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

    /** Admin oswa super admin (aksè ak administrasyon an). */
    public function isAdmin(): bool
    {
        return in_array($this->role, self::ADMINS, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::SUPER_ADMIN;
    }

    /** Wòl moun sa a gen dwa bay lòt kont. Admin: kliyan ak sekretè. Super admin: tout. */
    public function assignableRoles(): array
    {
        return match (true) {
            $this->isSuperAdmin() => self::ROLE_LABELS,
            $this->isAdmin()      => array_intersect_key(self::ROLE_LABELS, array_flip([self::CLIENT, self::SECRETARY])),
            default               => [],
        };
    }

    /** Èske moun sa a ka modifye / bloke / efase kont $other? */
    public function canManage(User $other): bool
    {
        if ($this->is($other)) {
            return false; // pwòp kont li: paj pwofil la sèlman
        }

        return array_key_exists($other->role, $this->assignableRoles());
    }

    public function isSecretary(): bool
    {
        return $this->role === self::SECRETARY;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, self::STAFF, true);
    }

    public function isBlocked(): bool
    {
        return $this->status === self::BLOCKED;
    }

    /** Ki paj yon itilizatè dwe ateri lè l fin konekte. */
    public function homeRoute(): string
    {
        return match ($this->role) {
            self::SUPER_ADMIN, self::ADMIN => route('admin.dashboard'),
            self::SECRETARY => route('counter.index'),
            default         => route('client.dashboard'),
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? 'Client';
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
