<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'divisi_id', 'no_hp', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

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

    // ═══════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    // ═══════════════════════════════════════════════════════════
    // FILAMENT PANEL ACCESS CONTROL
    // ═══════════════════════════════════════════════════════════

    /**
     * Tentukan siapa yang boleh masuk ke panel admin.
     * Anggota biasa TIDAK BOLEH masuk panel — hanya akses via mobile app.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasPermissionTo('akses_panel_admin');
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
