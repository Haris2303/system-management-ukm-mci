<?php
// ── app/Models/Pendaftar.php ──────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftar extends Model
{
    use HasFactory;

    protected $table = 'pendaftars';

    protected $fillable = [
        'divisi_id',
        'nama',
        'nim',
        'email',
        'no_hp',
        'angkatan',
        'status',
        'user_id',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jawabanPendaftars(): HasMany
    {
        return $this->hasMany(JawabanPendaftar::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'menunggu');
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'lulus');
    }

    public function scopeRejected(Builder $q): Builder
    {
        return $q->where('status', 'ditolak');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'menunggu';
    }

    public function isApproved(): bool
    {
        return $this->status === 'lulus';
    }

    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    /** Email efektif: gunakan email asli jika ada, fallback ke email dummy berbasis NIM */
    public function effectiveEmail(): string
    {
        return $this->email ?? $this->generateDummyEmail();
    }

    /** Total skor dari semua jawaban yang sudah dinilai */
    public function totalSkor(): int
    {
        return (int) $this->jawabanPendaftars()->whereNotNull('nilai_skor')->sum('nilai_skor');
    }

    /** Rata-rata skor */
    public function rataSkor(): float
    {
        $dinilai = $this->jawabanPendaftars()->whereNotNull('nilai_skor');
        if ($dinilai->count() === 0) return 0;
        return round($dinilai->avg('nilai_skor'), 1);
    }

    /** Email dummy untuk akun user: nim@mci.ac.id */
    public function generateDummyEmail(): string
    {
        return strtolower($this->nim) . '@mci.ac.id';
    }
}
