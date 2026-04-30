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

class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;  // 5 menit max
    public int $tries   = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly int $documentId) {}

    /**
     * Execute the job.
     */
    public function handle(PdfParserService $parser, EmbeddingService $embedding): void
    {
        $document = RagDocument::findOrFail($this->documentId);

        try {
            Log::info("RAG: Mulai proses PDF #{$document->id} — {$document->nama_file}");

            // 1. Parse PDF → chunks teks
            $filePath = Storage::disk('local')->path($document->path_file);
            $chunks   = $parser->parse($filePath);

            if (empty($chunks)) {
                $document->update(['status' => 'error']);
                Log::error("RAG: Tidak ada teks yang berhasil diekstrak dari PDF #{$document->id}");
                return;
            }

            Log::info("RAG: PDF #{$document->id} — {$document->nama_file} menghasilkan " . count($chunks) . " chunks");

            // 2. Batch embed chunks (maks 10 per request untuk hemat quota)
            $batchSize  = 10;
            $allChunks  = array_chunk($chunks, $batchSize);
            $chunkIndex = 0;

            foreach ($allChunks as $batch) {
                // Generate embedding untuk batch ini
                $embeddings = $embedding->embed($batch);

                foreach ($batch as $i => $content) {
                    $vector = $embeddings[$i] ?? null;
                    // validasi embedding
                    if (
                        empty($vector)
                        || !is_array($vector)
                    ) {
                        Log::warning('Embedding gagal untuk chunk', [
                            'chunk_index' => $chunkIndex,
                            'content' => substr($content, 0, 100),
                        ]);
                        continue;
                    }

                    // cek apakah vector isinya semua 0
                    $sum = array_sum(array_map('abs', $vector));

                    if ($sum == 0) {
                        Log::warning('Embedding kosong (all zero)', [
                            'chunk_index' => $chunkIndex,
                        ]);
                        continue;
                    }

                    RagChunk::create([
                        'document_id' => $document->id,
                        'chunk_index' => $chunkIndex++,
                        'content' => $content,
                        'embedding' => $vector,
                        'token_count' => (int)(mb_strlen($content) / 4),
                    ]);
                }

                // Jeda kecil agar tidak hit rate limit
                usleep(200_000); // 200ms
            }

            // 3. Update status dokumen
            $document->update([
                'status'       => 'ready',
                'total_chunks' => $chunkIndex,
            ]);

            Log::info("RAG: PDF #{$document->id} selesai diproses. Total chunks: {$chunkIndex}");
        } catch (\Throwable $e) {
            $document->update(['status' => 'error']);
            Log::error("RAG: Error proses PDF #{$document->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
