<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materis';

    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
        'link_url',
        'divisi_id',
        'uploaded_by',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Scopes ────────────────────────────────────────────────

    /**
     * Materi yang dapat diakses user:
     * - Materi umum (divisi_id NULL), ATAU
     * - Materi khusus divisi user tersebut
     */
    public function scopeUntukUser(Builder $q, ?int $divisiId): Builder
    {
        return $q->where(function (Builder $sub) use ($divisiId): void {
            $sub->whereNull('divisi_id');                 // Materi umum
            if ($divisiId !== null) {
                $sub->orWhere('divisi_id', $divisiId);    // Materi divisi user
            }
        });
    }

    public function scopeUmum(Builder $q): Builder
    {
        return $q->whereNull('divisi_id');
    }

    public function scopeKhususDivisi(Builder $q): Builder
    {
        return $q->whereNotNull('divisi_id');
    }

    // ── Helpers ───────────────────────────────────────────────

    /** URL publik file PDF (jika ada) */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? Storage::disk('public')->url($this->file_path)
            : null;
    }

    /** Ukuran file dalam format human-readable (contoh: "2.5 MB") */
    public function getFileSizeAttribute(): ?string
    {
        if (! $this->file_path || ! Storage::disk('public')->exists($this->file_path)) {
            return null;
        }

        $bytes = Storage::disk('public')->size($this->file_path);

        if ($bytes >= 1_048_576) return number_format($bytes / 1_048_576, 1) . ' MB';
        if ($bytes >= 1_024)     return number_format($bytes / 1_024, 1) . ' KB';
        return "{$bytes} B";
    }

    /** Apakah materi memiliki file PDF? */
    public function hasFile(): bool
    {
        return ! empty($this->file_path);
    }

    /** Apakah materi memiliki link eksternal? */
    public function hasLink(): bool
    {
        return ! empty($this->link_url);
    }

    /** Apakah materi ini umum (untuk semua divisi)? */
    public function isUmum(): bool
    {
        return $this->divisi_id === null;
    }
}
