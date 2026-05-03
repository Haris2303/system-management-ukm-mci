<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * EmbeddingService — menggunakan Google Gemini embedding-001
 *
 * Endpoint:
 *   https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent
 *
 * Output:
 *   Array float dengan 768 dimensi
 *
 * Daftar API key:
 *   https://aistudio.google.com/app/apikey
 *
 * Free tier:
 *   1500 requests/menit, gratis untuk pemakaian wajar.
 */
class EmbeddingService
{
    /** Dimensi embedding yang dihasilkan Gemini embedding-001 */
    public const DIMENSION = 768;

    /** Maksimal karakter per chunk (Gemini batas ~2048 token = ~8000 char) */
    public const MAX_CHARS = 6000;

    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey  = config('services.gemini.api_key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
        $this->model   = 'models/embedding-001';

        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'GEMINI_API_KEY belum diset di .env. '
                    . 'Dapatkan di: https://aistudio.google.com/app/apikey'
            );
        }
    }

    /**
     * Generate embedding untuk single text.
     *
     * @param string $text Teks yang akan di-embed
     * @param string $taskType RETRIEVAL_DOCUMENT (untuk indexing)
     *                         atau RETRIEVAL_QUERY (untuk pencarian user)
     * @return array<float> Vector 768 dimensi
     */
    public function embed(string $text, string $taskType = 'RETRIEVAL_DOCUMENT'): array
    {
        // Truncate jika terlalu panjang
        if (mb_strlen($text) > self::MAX_CHARS) {
            $text = mb_substr($text, 0, self::MAX_CHARS);
        }

        $url = "{$this->baseUrl}/{$this->model}:embedContent?key={$this->apiKey}";

        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->post($url, [
                'model' => $this->model,
                'content' => [
                    'parts' => [
                        ['text' => $text],
                    ],
                ],
                'taskType' => $taskType,
            ]);

        if ($response->failed()) {
            $errorMsg = $response->json('error.message', 'Unknown error');
            Log::error('Gemini embedding failed', [
                'status' => $response->status(),
                'error'  => $errorMsg,
            ]);
            throw new RuntimeException("Embedding gagal: {$errorMsg}");
        }

        $embedding = $response->json('embedding.values');

        if (! is_array($embedding) || count($embedding) !== self::DIMENSION) {
            throw new RuntimeException(
                'Embedding response tidak valid. Expected ' . self::DIMENSION . ' dimensi, got ' . count($embedding ?? [])
            );
        }

        return $embedding;
    }

    /**
     * Generate embedding khusus untuk query pencarian (lebih akurat untuk RAG).
     */
    public function embedQuery(string $query): array
    {
        return $this->embed($query, 'RETRIEVAL_QUERY');
    }

    /**
     * Hitung cosine similarity antara dua embedding vector.
     * Dipakai untuk ranking hasil pencarian.
     *
     * @return float Nilai antara -1 dan 1 (semakin tinggi semakin mirip)
     */
    public static function cosineSimilarity(array $vec1, array $vec2): float
    {
        if (count($vec1) !== count($vec2)) {
            throw new RuntimeException('Vector dimensions must match');
        }

        $dotProduct = 0.0;
        $norm1      = 0.0;
        $norm2      = 0.0;

        foreach ($vec1 as $i => $val) {
            $dotProduct += $val * $vec2[$i];
            $norm1      += $val * $val;
            $norm2      += $vec2[$i] * $vec2[$i];
        }

        if ($norm1 == 0 || $norm2 == 0) return 0.0;

        return $dotProduct / (sqrt($norm1) * sqrt($norm2));
    }
}
