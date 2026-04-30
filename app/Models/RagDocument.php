<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RagDocument extends Model
{
    protected $fillable = ['nama_file', 'path_file', 'deskripsi', 'total_chunks', 'status'];

    public function chunks(): HasMany
    {
        return $this->hasMany(RagChunk::class, 'document_id');
    }
}
