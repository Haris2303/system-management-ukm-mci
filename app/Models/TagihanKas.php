<?php

namespace App\Models;

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

    /**
     * Tandai tagihan ini sebagai lunas.
     * Otomatis set tanggal_bayar dengan waktu Asia/Jayapura.
     */
    public function tandaiLunas(): bool
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
        $bulanIndo = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        if (! preg_match('/^(\d{4})-(\d{2})$/', $this->bulan_tagihan, $m)) {
            return $this->bulan_tagihan;
        }

        return ($bulanIndo[$m[2]] ?? $m[2]) . ' ' . $m[1];
    }

    /**
     * Format nominal ke Rupiah (Rp 50.000).
     */
    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
