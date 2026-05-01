<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKas extends Model
{
    use HasFactory;

    protected $table = 'transaksi_kas';

    protected $fillable = [
        'jenis',
        'nominal',
        'keterangan',
        'tanggal',
        'bukti',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeMasuk(Builder $q): Builder
    {
        return $q->where('jenis', 'masuk');
    }

    public function scopeKeluar(Builder $q): Builder
    {
        return $q->where('jenis', 'keluar');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
