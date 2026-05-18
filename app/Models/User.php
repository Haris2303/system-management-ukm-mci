<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'divisi_id', 'no_hp', 'avatar', 'kicked_at', 'kicked_by', 'kicked_reason'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kicked_at'         => 'datetime',
            'password'          => 'hashed',
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
        if ($this->hasRole('demisioner') || $this->isKicked()) {
            return false;
        }

        return $this->hasPermissionTo('akses_panel_admin');
    }

    public function isAccountActive(): bool
    {
        return ! $this->hasRole('demisioner') && ! $this->isKicked();
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
