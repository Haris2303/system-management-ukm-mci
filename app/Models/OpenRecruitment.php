<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OpenRecruitment extends Model
{
    protected $fillable = [
        'judul',
        'gelombang',
        'deskripsi',
        'waktu_mulai',
        'waktu_selesai',
        'is_active',
        'catatan',
    ];

    protected $casts = [
        'waktu_mulai'  => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_active'    => 'boolean',
    ];

    /** Rekrutmen yang sedang aktif berdasarkan toggle dan rentang waktu */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now());
    }

    /** Apakah rekrutmen ini sedang berlangsung? */
    public function isSedangBerlangsung(): bool
    {
        return $this->is_active
            && now()->between($this->waktu_mulai, $this->waktu_selesai);
    }
}
