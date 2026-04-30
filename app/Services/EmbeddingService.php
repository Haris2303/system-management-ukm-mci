<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private string $apiKey;
    private string $model;
    private string $provider;

    public function __construct()
    {
        // Pilih provider dari .env
        // EMBEDDING_PROVIDER=voyage  (default, gratis tier tersedia)
        // EMBEDDING_PROVIDER=openai  (alternatif)
        // EMBEDDING_PROVIDER=tfidf   (tanpa API, fallback lokal)
        $this->provider = config('rag.embedding_provider', 'gemini');
        $this->apiKey   = config('rag.embedding_api_key', 'gemini');
        $this->model    = config('rag.embedding_model', 'gemini-embedding-001');
    }

    /**
     * Generate embedding vector dari teks.
     * Return array float[] atau null jika gagal.
     *
     * @param  string|string[]  $text
     * @return float[]|float[][]|null
     */
    public function embed(string|array $text): array|null
    {
        $texts = is_array($text) ? $text : [$text];

        return match ($this->provider) {
            'voyage' => $this->embedWithVoyage($texts),
            'openai' => $this->embedWithOpenAI($texts),
            'tfidf'  => $this->embedWithTFIDF($texts),
            'gemini' => $this->embedWithGemini($texts),
            default  => $this->embedWithVoyage($texts),
        };
    }

    // ── Voyage AI ─────────────────────────────────────────────
    private function embedWithVoyage(array $texts): array|null
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.voyageai.com/v1/embeddings', [
                'model' => $this->model,
                'input' => $texts,
            ]);

            if ($response->failed()) {
                Log::error('Voyage embedding error', ['body' => $response->body()]);
                return null;
            }

            $data = $response->json();
            return array_map(fn($item) => $item['embedding'], $data['data']);
        } catch (\Throwable $e) {
            Log::error('Voyage embedding exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ── OpenAI ────────────────────────────────────────────────
    private function embedWithOpenAI(array $texts): array|null
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/embeddings', [
                'model' => 'text-embedding-3-small',
                'input' => $texts,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI embedding error', ['body' => $response->body()]);
                return null;
            }

            $data = $response->json();
            return array_map(fn($item) => $item['embedding'], $data['data']);
        } catch (\Throwable $e) {
            Log::error('OpenAI embedding exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fallback TF-IDF sederhana (tanpa API key).
     * Kurang akurat tapi tetap fungsional untuk demo.
     */
    private function embedWithTFIDF(array $texts): array
    {
        return array_map(function (string $text): array {

            $text = mb_strtolower(trim($text));
            // ambil kata
            preg_match_all('/\b[\p{L}\p{N}]+\b/u', $text, $matches);

            $tokens = $matches[0] ?? [];

            if (empty($tokens)) {
                return array_fill(0, 256, 0.0);
            }

            $freq = array_count_values($tokens);
            $vector = array_fill(0, 256, 0.0);

            foreach ($freq as $token => $count) {
                $index = abs(crc32($token)) % 256;
                $vector[$index] += $count;
            }

            // normalize
            $magnitude = sqrt(array_sum(
                array_map(
                    fn($v) => $v * $v,
                    $vector
                )
            ));

            if ($magnitude == 0) {
                return $vector;
            }
            return array_map(
                fn($v) => $v / $magnitude,
                $vector
            );
        }, $texts);
    }

    private function embedWithGemini(array $texts): array|null
    {
        try {
            $vectors = [];
            foreach ($texts as $text) {
                $response = Http::timeout(60)
                    ->post(
                        'https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key='
                            . config('rag.gemini_api_key'),
                        [
                            'model' => 'models/gemini-embedding-004',
                            'content' => [
                                'parts' => [
                                    [
                                        'text' => $text
                                    ]
                                ]
                            ]
                        ]
                    );
                if ($response->failed()) {
                    Log::error('Gemini embedding error', [
                        'body' => $response->body()
                    ]);
                    return null;
                }
                $data = $response->json();
                $vectors[] =
                    $data['embedding']['values']
                    ?? [];
            }
            return $vectors;
        } catch (\Throwable $e) {
            Log::error('Gemini embedding exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }
}
