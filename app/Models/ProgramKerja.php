<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramKerja extends Model
{
    use HasFactory;

    protected $table = 'program_kerjas';

    protected $fillable = [
        'divisi_id',
        'nama_proker',
        'deskripsi',
        'pic_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'progress_persen',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'progress_persen' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function tugasProkers(): HasMany
    {
        return $this->hasMany(TugasProker::class, 'proker_id');
    }

    // ── Scopes ────────────────────────────────────────────────

    /**
     * Filter proker yang dapat diakses user:
     * - Proker umum (divisi_id NULL)
     * - Proker yang divisi_id-nya sama dengan divisi user
     */
    public function scopeUntukUser(Builder $q, ?int $divisiId): Builder
    {
        return $q->where(function (Builder $sub) use ($divisiId): void {
            $sub->whereNull('divisi_id');                // Proker umum
            if ($divisiId !== null) {
                $sub->orWhere('divisi_id', $divisiId);   // Proker divisi user
            }
        });
    }

    public function scopeAktif($q)
    {
        return $q->where('status', 'active');
    }

    public function scopeSelesai($q)
    {
        return $q->where('status', 'completed');
    }

    // ── Helpers ───────────────────────────────────────────────

    /** Hitung progress berdasarkan tugas yang selesai */
    public function hitungProgress(): int
    {
        $total = $this->tugasProkers()->count();
        if ($total === 0) return 0;

        $selesai = $this->tugasProkers()->where('is_selesai', true)->count();
        return (int) round(($selesai / $total) * 100);
    }

    /** Update kolom progress_persen — dipanggil oleh Observer */
    public function updateProgress(): void
    {
        $progress = $this->hitungProgress();

        // Update progress + auto-update status jika 100%
        $this->update([
            'progress_persen' => $progress,
            'status' => match (true) {
                $progress === 100 => 'completed',
                $progress > 0     => 'active',
                default           => $this->status, // jangan ubah kalau 0%
            },
        ]);
    }

    /** Apakah proker terlambat? (lewat tanggal_selesai tapi belum 100%) */
    public function isTerlambat(): bool
    {
        return now()->isAfter($this->tanggal_selesai)
            && $this->progress_persen < 100;
    }

    /** Sisa hari untuk deadline */
    public function sisaHari(): int
    {
        return (int) now()->diffInDays($this->tanggal_selesai, false);
    }

    /** Label status dengan emoji */
    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'planning'  => '📋 Perencanaan',
            'active'    => '🚀 Berjalan',
            'completed' => '✅ Selesai',
            default     => $this->status,
        };
    }

    /** Warna progress bar */
    public function getWarnaProgressAttribute(): string
    {
        if ($this->progress_persen === 100) return 'success';
        if ($this->isTerlambat())            return 'danger';
        if ($this->progress_persen >= 50)    return 'warning';
        return 'info';
    }
}
