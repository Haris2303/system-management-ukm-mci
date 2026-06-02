<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
        'tie_resolved_at',
        'tie_resolution_type',
        'tie_winner_candidate_id',
        'tie_resolution_notes',
        'parent_election_id',
    ];

    protected $casts = [
        'waktu_mulai'     => 'datetime',
        'waktu_selesai'   => 'datetime',
        'tie_resolved_at' => 'datetime',
        'is_anonim'       => 'boolean',
        'tampil_realtime' => 'boolean',
    ];

    // ── Auto-update status berdasarkan waktu ──────────────────
    protected static function booted(): void
    {
        static::retrieved(function (Election $election): void {
            if (
                $election->status === 'aktif'
                && $election->waktu_selesai !== null
                && now()->gt($election->waktu_selesai)
            ) {
                $election->updateQuietly(['status' => 'selesai']);
                $election->detectAndHandleTie();
            }
        });

        static::addGlobalScope('new_users_only', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('created_at', '>=', Auth::user()->created_at);
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

    public function tieWinnerCandidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'tie_winner_candidate_id');
    }

    public function parentElection(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'parent_election_id');
    }

    public function revoteElections(): HasMany
    {
        return $this->hasMany(Election::class, 'parent_election_id');
    }

    // ── Helpers ───────────────────────────────────────────────

    /** Apakah pemilihan sedang berlangsung? */
    public function isAktif(): bool
    {
        return $this->status === 'aktif'
            && $this->waktu_mulai !== null
            && $this->waktu_selesai !== null
            && now()->between($this->waktu_mulai, $this->waktu_selesai);
    }

    /** Apakah hasil boleh ditampilkan? */
    public function hasilBolehDitampilkan(): bool
    {
        return $this->tampil_realtime
            || $this->status === 'selesai'
            || $this->status === 'tie';
    }

    /**
     * Deteksi dan tangani hasil seri otomatis.
     * Dipanggil setelah status diubah ke 'selesai'.
     * Jika lebih dari satu kandidat berbagi suara tertinggi, ubah status ke 'tie' dan return true.
     */
    public function detectAndHandleTie(): bool
    {
        $candidates = $this->candidates()->withCount('votes as jumlah_suara')->get();
        $maxVotes   = $candidates->max('jumlah_suara');

        if (! $maxVotes) {
            return false;
        }

        if ($candidates->where('jumlah_suara', $maxVotes)->count() > 1) {
            $this->update(['status' => 'tie']);
            return true;
        }

        return false;
    }

    /** Apakah election berakhir seri? */
    public function isTie(): bool
    {
        return $this->status === 'tie';
    }

    /** Apakah seri sudah diselesaikan? */
    public function isTieResolved(): bool
    {
        return $this->tie_resolved_at !== null;
    }

    /** Kandidat-kandidat yang seri (berbagi suara terbanyak) saat status adalah 'tie' */
    public function getTiedCandidates(): Collection
    {
        $candidates = $this->candidates()->withCount('votes as jumlah_suara')->get();
        $maxVotes   = $candidates->max('jumlah_suara');

        return $candidates->where('jumlah_suara', $maxVotes)->values();
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
