<?php
// ── app/Services/RagService.php ───────────────────────────────
// Pipeline RAG utama:
// 1. Embed query user
// 2. Cari top-K chunks paling relevan (cosine similarity)
// 3. Bangun prompt dengan konteks
// 4. Panggil Claude API → stream jawaban

namespace App\Services;

// use App\Models\ChatHistory;
use App\Models\RagChunk;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RagService
{
    private int    $topK           = 4;      // Jumlah chunks yang diambil
    private float  $similarityThreshold = 0.1; // Min similarity agar chunk dipakai
    // private int    $maxHistoryTurns = 1;     // Berapa turn percakapan yang disertakan

    public function __construct(
        private readonly EmbeddingService $embedding,
    ) {}

    // ─────────────────────────────────────────────────────────
    // RETRIEVE: Cari chunks paling relevan untuk query
    // ─────────────────────────────────────────────────────────

    /**
     * @return Collection<RagChunk>
     */
    public function retrieve(string $query): Collection
    {
        // Embed query
        $query = mb_strtolower(trim($query));
        $queryVectors = $this->embedding->embed($query);

        if (empty($queryVectors)) {
            Log::warning('RAG: Gagal generate embedding untuk query', ['query' => $query]);
            return collect();
        }

        $queryVector = $queryVectors[0];

        // Ambil semua chunks yang punya embedding
        $chunks = RagChunk::whereNotNull('embedding')->get();

        if ($chunks->isEmpty()) {
            Log::warning('RAG: tidak ada chunks');
            return collect();
        }

        $scored = $chunks
            ->map(function (RagChunk $chunk) use ($queryVector) {

                return [
                    'chunk' => $chunk,
                    'score' => $chunk->cosineSimilarity($queryVector),
                ];
            })

            ->filter(
                function ($item) {
                    Log::info('Chunk selected', [
                        'score' => $item['score'],
                        'content' => substr(
                            $item['chunk']->content,
                            0,
                            150
                        ),
                    ]);

                    return $item['score'] > 0;
                }
                // fn($item) =>
                // $item['score'] > 0

            )

            ->sortByDesc('score')

            ->take($this->topK);

        Log::info('RAG retrieve', [
            'query' => $query,
            'found' => $scored->count(),
        ]);

        return $scored->pluck('chunk');
    }

    // ─────────────────────────────────────────────────────────
    // GENERATE: Panggil Claude API dengan konteks RAG
    // ─────────────────────────────────────────────────────────

    /**
     * Generate jawaban dari Claude berdasarkan konteks RAG.
     * Return generator untuk streaming (Server-Sent Events).
     *
     * @param  string              $query
     * @param  string              $sessionId
     * @param  Collection<RagChunk> $relevantChunks
     */
    public function generate(
        string     $query,
        // string     $sessionId,
        Collection $relevantChunks
    ): \Generator {
        // Simpan pesan user ke history
        // ChatHistory::create([
        //     'session_id' => $sessionId,
        //     'role'       => 'user',
        //     'content'    => $query,
        // ]);

        // Bangun konteks dari chunks
        $context = $relevantChunks->isEmpty()
            ? 'Tidak ada konteks ditemukan.'
            : $relevantChunks
            ->map(function ($chunk) {
                return "
                === CONTEXT ===
                " . mb_substr($chunk->content, 0, 300) . "";
            })->join("\n");

        if ($relevantChunks->isEmpty()) {
            yield "Maaf, aku belum punya informasi tentang itu 🙏";
            return;
        }

        // Ambil history percakapan sebelumnya
        // $history = ChatHistory::where('session_id', $sessionId)
        //     ->orderBy('id', 'desc')
        //     ->take($this->maxHistoryTurns * 2)
        //     ->get()
        //     ->reverse()
        //     ->values();

        // format history
        // $conversationHistory = $history

        //     ->map(function ($chat) {
        //         return strtoupper($chat->role)
        //             . ': '
        //             . $chat->content;
        //     })->join("\n");

        // // Bangun messages array untuk Claude
        // $messages = $history->map(fn(ChatHistory $h) => [
        //     'role'    => $h->role,
        //     'content' => $h->content,
        // ])->toArray();

        // System prompt
        // system prompt
        $systemPrompt = $this->buildSystemPrompt();

        // gabungkan prompt
        $prompt = "
            {$systemPrompt}

            ========================
            KONTEKS DOKUMEN
            ========================

            {$context}

            ========================
            PERTANYAAN USER
            ========================

            {$query}

            ";

        // Panggil Claude API dengan streaming
        $fullResponse = '';

        try {
            $normalizedQuery = strtolower(trim($query));
            $cacheKey = 'rag:' . md5($normalizedQuery);
            if (Cache::has($cacheKey)) {
                yield Cache::get($cacheKey);
                return;
            }

            $response = Http::timeout(60)
                ->retry(
                    1,
                    1000,
                    function ($exception, $request) {
                        return true;
                    }
                )
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key='
                        . config('rag.gemini_api_key'),
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => $prompt
                                    ]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.3,
                            'maxOutputTokens' => 200,
                        ],
                    ]
                );

            // debug jika gagal
            if ($response->failed()) {
                $status = $response->status();

                Log::error('Gemini API Error', [
                    'status' => $status,
                    'body' => $response->body(),
                ]);
                $fallback = match ($status) {
                    503 =>
                    'Server AI sedang sibuk 😅 coba beberapa detik lagi.',
                    429 =>
                    'Limit AI tercapai 🙏 coba lagi nanti.',
                    default =>
                    'Terjadi gangguan AI 🙏',
                };
                yield $fallback;
                return;
            }
            $data = $response->json();
            Log::info('Gemini response', [
                'response' => $data
            ]);
            $text =
                $data['candidates'][0]['content']['parts'][0]['text']
                ??
                'Maaf, saya tidak menemukan jawaban.';

            $fullResponse = $text;

            Cache::put(
                $cacheKey,
                $text,
                now()->addHours(6)
            );

            // kirim ke frontend
            yield $fullResponse;
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            Log::error('Gemini Exception', [
                'message' => $message
            ]);

            if (str_contains($message, '429')) {
                yield 'AI sedang mencapai limit 😅 coba lagi sebentar ya.';
                return;
            }

            if (str_contains($message, '503')) {
                yield 'AI sedang sibuk 😭 coba lagi beberapa detik ya.';
                return;
            }

            yield 'Terjadi gangguan sistem 🙏';
        }

        // Simpan respons assistant ke history
        // if (!empty($fullResponse)) {
        //     // simpan jawaban AI
        //     ChatHistory::create([
        //         'session_id' => $sessionId,
        //         'role' => 'assistant',
        //         'content' => $fullResponse,
        //         'sources' => $relevantChunks
        //             ->map(fn($c) => [
        //                 'chunk_id' => $c->id,
        //                 'document_id' => $c->document_id,
        //             ])
        //             ->toArray(),
        //     ]);
        // }
    }
    // ─────────────────────────────────────────────
    // SYSTEM PROMPT
    // ─────────────────────────────────────────────

    private function buildSystemPrompt(): string
    {
        return "

        Kamu adalah AI Assistant UKM MCI Universitas Muhammadiyah Sorong.

        Tugasmu: membantu menjawab pertanyaan tentang UKM MCI

        Jawab pertanyaan hanya berdasarkan konteks yang diberikan.

        Jika informasi tidak tersedia,
        katakan dengan jujur bahwa informasi tidak ditemukan.

        Gaya bicara:

        - ramah
        - natural
        - seperti kakak tingkat
        - gunakan bahasa indonesia santai profesional
        - boleh gunakan emoji secukupnya

        ";
        // return "
        //     Kamu adalah MCI AI Assistant.

        //     Kamu adalah asisten virtual resmi UKM MCI
        //     (Media Creative Informations)
        //     Universitas Muhammadiyah Sorong.

        //     Tugasmu:

        //     - membantu menjawab pertanyaan tentang UKM MCI
        //     - menjelaskan divisi
        //     - menjelaskan kegiatan
        //     - membantu informasi pendaftaran
        //     - menjelaskan manfaat bergabung

        //     Gaya bicara:

        //     - ramah
        //     - natural
        //     - seperti kakak tingkat
        //     - gunakan bahasa indonesia santai profesional
        //     - boleh gunakan emoji secukupnya

        //     ATURAN PENTING:

        //     - jangan mengarang jawaban
        //     - gunakan konteks dokumen sebagai sumber utama
        //     - jika jawaban tidak ditemukan di konteks,
        //     katakan dengan jujur bahwa kamu tidak mempunya informasi mengenai itu
        //     - jangan menjawab di luar topik UKM MCI

        //     FORMAT:

        //     - gunakan bullet point jika perlu
        //     - jawaban singkat dan jelas
        //     - maksimal 3 paragraf

        //     ";
    }
}
