<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RagDocument extends Model
{
    protected $fillable = ['nama_file', 'path_file', 'deskripsi', 'total_chunks', 'status'];

    protected $casts = [
        'total_chunks' => 'integer',
    ];

    public function chunks(): HasMany
    {
        return $this->hasMany(RagChunk::class, 'document_id');
    }

    /**
     * ⭐ COMPATIBILITY ALIAS
     *
     * Beberapa kode lama mungkin panggil $ragDocument->document
     * (mengira ini RagChunk). Method ini bikin "self-reference"
     * agar tidak error.
     *
     * Bisa dihapus setelah semua kode lama di-cleanup.
     */
    public function getDocumentAttribute(): self
    {
        return $this;
    }
}
