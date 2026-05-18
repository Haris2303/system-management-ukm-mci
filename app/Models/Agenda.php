<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_agenda',
        'deskripsi',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'is_active',
        'qr_code_token'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Booted method: auto-generate qr_code_token saat agenda dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function (Agenda $agenda): void {
            if (empty($agenda->qr_code_token)) {
                $agenda->qr_code_token = Str::random(32);
            }
        });

        static::retrieved(function (Agenda $agenda): void {
            if ($agenda->is_active && now('Asia/Jayapura')->gt($agenda->waktu_selesai)) {
                $agenda->tutup();
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function presensisHadir(): HasMany
    {
        return $this->hasMany(Presensi::class)->where('status', 'Hadir');
    }

    // ── Actions ────────────────────────────────────────────────────

    /**
     * Tutup agenda: catat Absen untuk semua anggota aktif yang belum hadir,
     * lalu set is_active = false.
     */
    public function tutup(): void
    {
        if (! $this->is_active) {
            return;
        }

        $jamAbsen = $this->waktu_selesai ?? Carbon::now('Asia/Jayapura');

        $sudahHadirIds = $this->presensis()->pluck('user_id');

        User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'demisioner']))
            ->whereNotIn('id', $sudahHadirIds)
            ->pluck('id')
            ->each(fn ($userId) => Presensi::create([
                'user_id'   => $userId,
                'agenda_id' => $this->id,
                'status'    => 'Absen',
                'jam_hadir' => $jamAbsen,
            ]));

        $this->update(['is_active' => false]);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeAktif(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    // ── Accessors ──────────────────────────────────────────────────

    public function getStatusWaktuAttribute(): string
    {
        return now()->gt($this->waktu_selesai) ? 'Selesai' : 'Aktif';
    }
}
