<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasProker extends Model
{
    use HasFactory;

    protected $table = 'tugas_prokers';

    protected $fillable = [
        'proker_id',
        'nama_tugas',
        'is_selesai',
        'urut',
    ];

    protected $casts = [
        'is_selesai' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class, 'proker_id');
    }
}
