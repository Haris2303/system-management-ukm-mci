<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'user_id',
        'visi',
        'misi',
        'foto',
        'urut',
    ];

    protected static function booted(): void
    {
        static::creating(function (Candidate $candidate) {
            if (! $candidate->urut) {
                $candidate->urut = static::where('election_id', $candidate->election_id)->max('urut') + 1;
            }
        });
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /** URL foto kandidat — fallback ke avatar profil jika tidak ada foto */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }

        return $this->user?->avatar_url;
    }

    /** Jumlah suara yang diterima kandidat ini */
    public function jumlahSuara(): int
    {
        return $this->votes()->count();
    }

    /** Persentase suara dari total */
    public function persentase(int $totalSuara): float
    {
        if ($totalSuara === 0) return 0;
        return round(($this->jumlahSuara() / $totalSuara) * 100, 1);
    }
}
