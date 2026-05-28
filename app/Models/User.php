<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasAvatar;
use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasAvatar, HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'divisi_id', 'no_hp', 'avatar',
        'last_photo_path', 'photo_uploaded_at', 'kicked_at', 'kicked_by',
        'kicked_reason', 'public_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->public_id)) {
                $user->public_id = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'kicked_at'          => 'datetime',
            'photo_uploaded_at'  => 'datetime',
            'password'           => 'hashed',
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function kickedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kicked_by');
    }

    // ═══════════════════════════════════════════════════════════
    // FILAMENT PANEL ACCESS CONTROL
    // ═══════════════════════════════════════════════════════════

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole('demisioner') || $this->hasRole('anggota') || $this->isKicked()) {
            return false;
        }

        return $this->hasPermissionTo('akses_panel_admin');
    }

    public function isAccountActive(): bool
    {
        return ! $this->hasRole('demisioner')
            && ! $this->hasRole('anggota')
            && ! $this->isKicked();
    }

    // ═══════════════════════════════════════════════════════════
    // HELPER METHODS
    // ═══════════════════════════════════════════════════════════

    /** Apakah user adalah Super Admin? */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /** Apakah user adalah Ketua Divisi? */
    public function isKetuaDivisi(): bool
    {
        return $this->hasRole('ketua_divisi');
    }

    /** Apakah user adalah demisioner (akun nonaktif) */
    public function isDemisioner(): bool
    {
        return $this->hasRole('demisioner');
    }

    /** Apakah user sudah di-kick (dikeluarkan) */
    public function isKicked(): bool
    {
        return $this->kicked_at !== null;
    }

    /** Apakah user boleh upload foto profil baru (cooldown 2 minggu) */
    public function canUploadPhoto(): bool
    {
        if (! $this->photo_uploaded_at) return true;

        return $this->photo_uploaded_at->copy()->addWeeks(2)->isPast();
    }

    /** Waktu cooldown upload foto berakhir, null jika sudah bisa upload */
    public function photoUploadCooldownEndsAt(): ?\Illuminate\Support\Carbon
    {
        if (! $this->photo_uploaded_at) return null;

        $endsAt = $this->photo_uploaded_at->copy()->addWeeks(2);

        return $endsAt->isFuture() ? $endsAt : null;
    }

    /** Label role utama yang ramah dibaca */
    public function getRoleLabelAttribute(): string
    {
        $role = $this->roles->first()?->name ?? 'anggota';

        return match ($role) {
            'super_admin'  => '👑 Super Admin',
            'ketua_ukm'    => '👨‍💼 Ketua UKM',
            'sekretaris'   => '📝 Sekretaris',
            'bendahara'    => '💰 Bendahara',
            'ketua_divisi' => '🏆 Ketua Divisi',
            'anggota'      => '👥 Anggota',
            default        => $role,
        };
    }
}
