<?php

namespace App\Jobs;

use App\Models\RagChunk;
use App\Models\RagDocument;
use App\Services\EmbeddingService;
use App\Services\PdfParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Job: Process PDF setelah upload.
 *
 * Pipeline:
 *   1. Parse PDF → text
 *   2. Chunk text per ~500 char (dengan overlap 50)
 *   3. Generate embedding per chunk (Gemini embedding-001)
 *   4. Simpan ke tabel rag_chunks
 *   5. Update status di rag_documents
 */
class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const CHUNK_SIZE    = 500;
    public const CHUNK_OVERLAP = 50;

    public int $timeout = 600;
    public int $tries   = 2;

    public function __construct(
        public int $documentId,
    ) {}

    public function handle(
        PdfParserService $parser,
        EmbeddingService $embedding,
    ): void {
        $document = RagDocument::find($this->documentId);

        if (! $document) {
            Log::warning("ProcessPdfJob: Document #{$this->documentId} tidak ditemukan.");
            return;
        }

        try {
            $document->update(['status' => 'processing']);

            // 1. Parse PDF jadi text
            //    Pakai disk 'local' karena upload ke storage/app/rag-docs/
            $absolutePath = Storage::disk('local')->path($document->path_file);
            $text = $parser->extract($absolutePath);

            if (empty(trim($text))) {
                throw new \RuntimeException('PDF tidak mengandung teks (mungkin scan/image).');
            }

            // 2. Hapus chunks lama (jika reprocess)
            $document->chunks()->delete();
            $document->update(['total_chunks' => 0]);

            // 3. Split text → chunks dengan overlap
            $chunks = $this->splitIntoChunks($text);
            Log::info("Document #{$document->id} dipecah jadi " . count($chunks) . " chunks.");

            // 4. Embed & save tiap chunk ke tabel rag_chunks
            foreach ($chunks as $i => $chunkText) {
                try {
                    $vector = $embedding->embed($chunkText, 'RETRIEVAL_DOCUMENT');

                    RagChunk::create([
                        'document_id' => $document->id,
                        'chunk_index' => $i + 1,
                        'content'     => $chunkText,
                        'embedding'   => $vector,
                        'token_count' => (int) (mb_strlen($chunkText) / 4),  // estimasi kasar
                    ]);

                    // Update progress setelah setiap chunk berhasil disimpan
                    $document->increment('total_chunks');

                    // Delay kecil untuk hindari rate limit Gemini
                    usleep(150_000); // 150ms

                } catch (\Throwable $e) {
                    Log::error("Chunk #{$i} dari Document #{$document->id} gagal: " . $e->getMessage());
                }
            }

            // 5. Update status sukses
            $document->update(['status' => 'ready']);

            Log::info("✅ Document #{$document->id} berhasil diproses ({$totalChunks} chunks).");
        } catch (\Throwable $e) {
            $document->update(['status' => 'error']);
            Log::error("❌ Document #{$document->id} gagal diproses: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Split text jadi chunks dengan overlap antar chunk.
     */
    private function splitIntoChunks(string $text): array
    {
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        $chunks = [];
        $start  = 0;
        $length = mb_strlen($text);

        while ($start < $length) {
            $chunk = mb_substr($text, $start, self::CHUNK_SIZE);

            // Coba potong di akhir kalimat terdekat
            if ($start + self::CHUNK_SIZE < $length) {
                $lastPeriod = max(
                    mb_strrpos($chunk, '. '),
                    mb_strrpos($chunk, '? '),
                    mb_strrpos($chunk, '! ')
                );

                if ($lastPeriod !== false && $lastPeriod > self::CHUNK_SIZE * 0.5) {
                    $chunk = mb_substr($chunk, 0, $lastPeriod + 1);
                }
            }

            $chunks[] = trim($chunk);
            $start   += mb_strlen($chunk) - self::CHUNK_OVERLAP;

            if (mb_strlen($chunk) <= self::CHUNK_OVERLAP) {
                $start += self::CHUNK_SIZE;
            }
        }

        return array_filter($chunks, fn($c) => mb_strlen(trim($c)) > 50);
    }
}
