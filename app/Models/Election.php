<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'posisi',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'is_anonim',
        'tampil_realtime',
        'created_by',
    ];

    protected $casts = [
        'waktu_mulai'     => 'datetime',
        'waktu_selesai'   => 'datetime',
        'is_anonim'       => 'boolean',
        'tampil_realtime' => 'boolean',
    ];

    // ── Auto-update status berdasarkan waktu ──────────────────
    protected static function booted(): void
    {
        static::retrieved(function (Election $election): void {
            if ($election->status === 'aktif' && now()->gt($election->waktu_selesai)) {
                $election->updateQuietly(['status' => 'selesai']);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class)->orderBy('urut');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ───────────────────────────────────────────────

    /** Apakah pemilihan sedang berlangsung? */
    public function isAktif(): bool
    {
        return $this->status === 'aktif'
            && now()->between($this->waktu_mulai, $this->waktu_selesai);
    }

    /** Apakah hasil boleh ditampilkan? */
    public function hasilBolehDitampilkan(): bool
    {
        // Tampil realtime: selalu tampil saat aktif
        // Tidak realtime: hanya tampil setelah selesai
        return $this->tampil_realtime || $this->status === 'selesai';
    }

    /** Cek apakah user sudah vote (via hash) */
    public function sudahDivote(int $userId): bool
    {
        $hash = hash('sha256', $userId . '-' . $this->id . '-ukm-mci-secret');
        return $this->votes()->where('voter_hash', $hash)->exists();
    }

    /** Total suara masuk */
    public function totalSuara(): int
    {
        return $this->votes()->count();
    }
}
