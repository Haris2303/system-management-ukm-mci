<?php

namespace App\Services;

use App\Models\RagChunk;
use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RagService
{
    // private const MODEL          = 'openai/gpt-oss-120b:free';
    private const MODEL          = 'openrouter/free';
    private const ENDPOINT       = 'https://openrouter.ai/api/v1/chat/completions';
    private const TOP_K          = 5;
    private const MIN_SIMILARITY = 0.3;

    private string $apiKey;
    private EmbeddingService $embedding;

    public function __construct(EmbeddingService $embedding)
    {
        $this->embedding = $embedding;
        $this->apiKey    = config('services.openrouter.api_key');

        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'OPENROUTER_API_KEY belum diset di .env. '
                    . 'Daftar gratis di: https://openrouter.ai/keys'
            );
        }
    }

    // ═══════════════════════════════════════════════════════════
    // PUBLIC API
    // ═══════════════════════════════════════════════════════════

    public function ask(string $question): array
    {
        $chunks = $this->retrieve($question);

        $answer = '';
        foreach ($this->generate($question, $chunks) as $piece) {
            $answer .= $piece;
        }

        return [
            'answer'      => trim($answer),
            'sources'     => $this->formatSources($chunks),
            'has_context' => $chunks->isNotEmpty(),
        ];
    }

    /**
     * Retrieve top-K chunks dari tabel rag_chunks (bukan rag_documents!).
     */
    public function retrieve(string $question): Collection
    {
        $queryEmbedding = $this->embedding->embedQuery($question);

        // ⭐ Query ke tabel rag_chunks, bukan rag_documents
        $allChunks = RagChunk::with('document')
            ->whereNotNull('embedding')
            ->get();

        $scored = $allChunks->map(function (RagChunk $chunk) use ($queryEmbedding) {
            // Karena $casts['embedding'] = 'array', otomatis array — tidak perlu json_decode
            $vec = $chunk->embedding;

            if (! is_array($vec) || count($vec) !== EmbeddingService::DIMENSION) {
                return null;
            }

            $chunk->similarity = EmbeddingService::cosineSimilarity($queryEmbedding, $vec);
            return $chunk;
        })->filter();

        return $scored
            ->sortByDesc('similarity')
            ->filter(fn($c) => $c->similarity >= self::MIN_SIMILARITY)
            ->take(self::TOP_K)
            ->values();
    }

    /**
     * Generate jawaban streaming via OpenRouter.
     * @return Generator<string>
     */
    public function generate(string $question, Collection $chunks): Generator
    {
        $hasContext   = $chunks->isNotEmpty();
        $context      = $this->buildContext($chunks);
        $systemPrompt = $this->buildSystemPrompt($hasContext);
        $userPrompt   = $this->buildUserPrompt($question, $context, $hasContext);

        $ch = curl_init(self::ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$this->apiKey}",
                'Content-Type: application/json',
                'HTTP-Referer: ' . config('app.url', 'http://localhost'),
                'X-Title: UKM MCI Chatbot',
            ],
            CURLOPT_POSTFIELDS     => json_encode([
                'model'       => self::MODEL,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'temperature' => 0.3,
                'max_tokens'  => 1024,
                'top_p'       => 0.9,
                'stream'      => true,
            ]),
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            yield "Maaf, terjadi gangguan koneksi: {$err}";
            return;
        }
        curl_close($ch);

        if ($httpCode !== 200) {
            $errData = json_decode($rawResponse, true);
            $errMsg  = $errData['error']['message'] ?? 'Unknown error';
            Log::error('OpenRouter streaming failed', [
                'status' => $httpCode,
                'error'  => $errMsg,
            ]);
            yield "Maaf, AI sedang sibuk. Silakan coba lagi sebentar lagi.";
            return;
        }

        // Parse SSE response
        $hasYielded = false;
        foreach (explode("\n", $rawResponse) as $line) {
            $line = trim($line);
            if (empty($line) || ! str_starts_with($line, 'data: ')) continue;

            $data = substr($line, 6);
            if ($data === '[DONE]') break;

            $json = json_decode($data, true);
            if (! is_array($json)) continue;

            $piece = $json['choices'][0]['delta']['content'] ?? '';
            if ($piece !== '') {
                $hasYielded = true;
                yield $piece;
            }
        }

        if (! $hasYielded) {
            yield 'Maaf, saya belum bisa menjawab pertanyaan ini. Silakan coba pertanyaan lain.';
        }
    }

    // ═══════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════

    private function buildContext(Collection $chunks): string
    {
        if ($chunks->isEmpty()) return '';

        $parts = [];
        foreach ($chunks as $i => $chunk) {
            $no      = $i + 1;
            $source  = $chunk->document->nama_file ?? 'Dokumen';
            $content = trim($chunk->content);
            $parts[] = "[Sumber {$no}: {$source}]\n{$content}";
        }

        return implode("\n\n---\n\n", $parts);
    }

    private function buildSystemPrompt(bool $hasContext): string
    {
        $base = <<<'PROMPT'
Kamu adalah AI Assistant UKM MCI Universitas Muhammadiyah Sorong.

Tugasmu: membantu menjawab pertanyaan tentang UKM MCI

Jawab pertanyaan hanya berdasarkan konteks yang diberikan.

Gaya bicara:

- ramah
- natural
- seperti kakak tingkat
- gunakan bahasa indonesia santai profesional
- boleh gunakan emoji secukupnya

ATURAN PENTING:
1. Selalu jawab dalam Bahasa Indonesia yang ramah dan profesional seperti kakak tingkat.
2. Jangan mengarang informasi. Jika tidak tahu, katakan dengan jujur.
3. Jika konteks tersedia, prioritaskan informasi dari konteks tersebut.
4. Jawaban singkat, jelas, langsung ke poin (maksimal 3 paragraf).
5. Gunakan emoji secukupnya untuk memperjelas (jangan berlebihan).
6. Jika user bertanya hal di luar topik UKM MCI, arahkan kembali dengan sopan.
7. Jangan bawa nama portal kampus atau semacamnya.
8. Jangan gunakan kata kata seperti "belum menemukan informasi di dokumen yang ada".
PROMPT;

        if (! $hasContext) {
            $base .= "\n\nCATATAN: Tidak ada konteks dokumen yang relevan. "
                . "Jawablah berdasarkan pengetahuan umum tentang UKM teknologi mahasiswa, "
                . "atau sarankan user menghubungi pengurus untuk info detail.";
        }

        return $base;
    }

    private function buildUserPrompt(string $question, string $context, bool $hasContext): string
    {
        if (! $hasContext) {
            return "Pertanyaan: {$question}";
        }

        return <<<PROMPT
        Berikut konteks dari dokumen UKM MCI yang relevan:

        {$context}

        ---

        Berdasarkan konteks di atas, jawab pertanyaan berikut:

        Pertanyaan: {$question}
        PROMPT;
    }

    private function formatSources(Collection $chunks): array
    {
        return $chunks->map(fn($c) => [
            'document'   => $c->document->nama_file ?? '—',
            'similarity' => round($c->similarity ?? 0, 3),
            'preview'    => mb_substr($c->content, 0, 150) . '...',
        ])->values()->toArray();
    }
}
