<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisis';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'icon',
        'ketua',
        'is_active',
        'urut',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Auto-generate slug ────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (Divisi $divisi): void {
            if (empty($divisi->slug)) {
                $divisi->slug = Str::slug($divisi->nama);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────

    public function pertanyaanSeleksis(): HasMany
    {
        return $this->hasMany(PertanyaanSeleksi::class);
    }

    public function pendaftars(): HasMany
    {
        return $this->hasMany(Pendaftar::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeAktif($q)
    {
        return $q->where('is_active', true)->orderBy('urut');
    }

    // ── Helper: ambil pertanyaan aktif untuk form publik ──────
    public function pertanyaanAktif()
    {
        return $this->pertanyaanSeleksis()
            ->where('is_active', true)
            ->orderBy('urut')
            ->get();
    }
}
