<?php

namespace App\Models;

use App\Helpers\BulanHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanKas extends Model
{
    use HasFactory;

    protected $table = 'tagihan_kas';

    protected $fillable = [
        'user_id',
        'bulan_tagihan',
        'nominal',
        'status',
        'tanggal_bayar',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'nominal'       => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeBelumDibayar(Builder $q): Builder
    {
        return $q->where('status', 'belum_dibayar');
    }

    public function scopeLunas(Builder $q): Builder
    {
        return $q->where('status', 'lunas');
    }

    public function scopeMilikUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === 'lunas';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'belum_dibayar';
    }

    /**
     * Tandai tagihan ini sebagai lunas.
     * Otomatis set tanggal_bayar dengan waktu Asia/Jayapura.
     */
    public function markAsPaid(): bool
    {
        return $this->update([
            'status'        => 'lunas',
            'tanggal_bayar' => now('Asia/Jayapura'),
        ]);
    }

    /**
     * Format bulan_tagihan ke nama bulan Indonesia.
     * Contoh: "2025-01" → "Januari 2025"
     */
    public function getBulanTagihanFormatAttribute(): string
    {
        return BulanHelper::format($this->bulan_tagihan);
    }

    /**
     * Format nominal ke Rupiah (Rp 50.000).
     */
    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
