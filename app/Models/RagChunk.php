<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RagChunk extends Model
{
    protected $fillable = ['document_id', 'chunk_index', 'content', 'embedding', 'token_count'];

    protected $casts = [
        'embedding' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(RagDocument::class, 'document_id');
    }

    /**
     * Hitung cosine similarity antara embedding chunk ini
     * dengan query embedding yang diberikan.
     *
     * @param  float[]  $queryEmbedding
     */
    public function cosineSimilarity(array $queryEmbedding): float
    {
        $chunkEmb = $this->embedding;

        if (empty($chunkEmb) || count($chunkEmb) !== count($queryEmbedding)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($chunkEmb as $i => $val) {
            $dot   += $val * $queryEmbedding[$i];
            $normA += $val * $val;
            $normB += $queryEmbedding[$i] * $queryEmbedding[$i];
        }

        $denom = sqrt($normA) * sqrt($normB);
        return $denom > 0 ? $dot / $denom : 0.0;
    }
}
