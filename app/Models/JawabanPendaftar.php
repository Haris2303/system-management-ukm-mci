<?php
// ── app/Models/JawabanPendaftar.php ──────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanPendaftar extends Model
{
    protected $table = 'jawaban_pendaftars';

    protected $fillable = [
        'pendaftar_id',
        'pertanyaan_id',
        'jawaban_teks',
        'nilai_skor',
    ];

    protected $casts = [
        'nilai_skor' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(Pendaftar::class);
    }

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(PertanyaanSeleksi::class, 'pertanyaan_id');
    }
}
