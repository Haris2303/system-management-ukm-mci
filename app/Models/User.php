<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'divisi_id', 'no_hp', 'avatar', 'last_photo_path', 'photo_uploaded_at', 'kicked_at', 'kicked_by', 'kicked_reason', 'public_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

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

    /** URL avatar siap pakai — menangani path storage, URL eksternal, dan emoji preset */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) return null;

        if (str_starts_with($this->avatar, 'emoji:')) {
            [, $emoji, $color] = explode(':', $this->avatar, 3);
            return static::emojiAvatarDataUrl($emoji, $color);
        }

        if (str_starts_with($this->avatar, 'http')) return $this->avatar;

        return asset('storage/' . $this->avatar);
    }

    public static function emojiAvatarDataUrl(string $emoji, string $color): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">'
            . '<circle cx="64" cy="64" r="64" fill="#' . htmlspecialchars($color) . '"/>'
            . '<text x="64" y="68" font-size="72" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($emoji) . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

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
