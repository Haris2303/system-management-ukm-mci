<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PertanyaanSeleksi extends Model
{
    use HasFactory;

    protected $table = 'pertanyaan_seleksis';

    protected $fillable = [
        'divisi_id',
        'pertanyaan_teks',
        'is_active',
        'urut',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jawabanPendaftars(): HasMany
    {
        return $this->hasMany(JawabanPendaftar::class, 'pertanyaan_id');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }
}
