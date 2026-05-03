<?php

namespace App\Console\Commands;

use App\Models\RagChunk;
use App\Models\RagDocument;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;

/**
 * Command untuk hapus semua embedding lama dan generate ulang
 * dengan Gemini embedding-001 (768 dim).
 *
 * Penggunaan:
 *   php artisan rag:reembed
 *   php artisan rag:reembed --force
 *   php artisan rag:reembed --chunk=50 --delay=200
 */
class ReembedAllChunks extends Command
{
    protected $signature = 'rag:reembed
                            {--force : Skip konfirmasi}
                            {--chunk=50 : Jumlah chunk per batch (hindari rate limit)}
                            {--delay=200 : Delay antar request dalam ms}';

    protected $description = 'Re-embed semua chunk dengan Gemini embedding-001 (hapus embedding lama)';

    public function handle(EmbeddingService $embeddingService): int
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════');
        $this->info('🔄 Re-embed Semua Chunks dengan Gemini');
        $this->info('═══════════════════════════════════════════════════');
        $this->newLine();

        $total = RagDocument::count();

        if ($total === 0) {
            $this->warn('❌ Tidak ada chunks di database. Upload PDF dulu di admin.');
            return self::SUCCESS;
        }

        $this->line("📊 Total chunks  : <fg=cyan>{$total}</>");
        $this->line("📐 Dimensi target: <fg=cyan>" . EmbeddingService::DIMENSION . "</> (Gemini embedding-001)");
        $this->newLine();

        // Konfirmasi (kecuali --force)
        if (! $this->option('force')) {
            $confirmed = $this->confirm(
                "⚠️  Semua embedding lama akan DIHAPUS dan di-generate ulang. Lanjutkan?",
                false
            );
            if (! $confirmed) {
                $this->info('❌ Dibatalkan.');
                return self::SUCCESS;
            }
        }

        // ── 1. Hapus semua embedding lama ──────────────────────
        $this->newLine();
        $this->info('🗑️  Menghapus embedding lama...');
        RagChunk::query()->update(['embedding' => null]);
        $this->info('✅ Embedding lama dihapus.');

        // ── 2. Re-embed batch per batch ────────────────────────
        $chunkSize = (int) $this->option('chunk');
        $delayMs   = (int) $this->option('delay');

        $this->newLine();
        $this->info("🚀 Mulai re-embed (batch {$chunkSize}, delay {$delayMs}ms)...");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat('verbose');
        $bar->start();

        $sukses = 0;
        $gagal  = 0;
        $errors = [];

        RagDocument::with('document')
            ->chunk($chunkSize, function ($chunks) use (
                $embeddingService,
                $bar,
                &$sukses,
                &$gagal,
                &$errors,
                $delayMs
            ) {
                foreach ($chunks as $chunk) {
                    try {
                        $vector = $embeddingService->embed(
                            $chunk->content,
                            'RETRIEVAL_DOCUMENT'
                        );

                        $chunk->update(['embedding' => $vector]);
                        $sukses++;
                    } catch (\Throwable $e) {
                        $gagal++;
                        $errors[] = "Chunk #{$chunk->id}: " . $e->getMessage();
                    }

                    $bar->advance();

                    // Delay agar tidak kena rate limit Gemini
                    if ($delayMs > 0) {
                        usleep($delayMs * 1000);
                    }
                }
            });

        $bar->finish();
        $this->newLine(2);

        // ── 3. Report ──────────────────────────────────────────
        $this->info('═══════════════════════════════════════════════════');
        $this->info('📊 Hasil Re-embedding');
        $this->info('═══════════════════════════════════════════════════');
        $this->line("✅ Berhasil : <fg=green>{$sukses}</> chunks");
        $this->line("❌ Gagal    : <fg=red>{$gagal}</> chunks");

        if (! empty($errors)) {
            $this->newLine();
            $this->error('Error log (5 pertama):');
            foreach (array_slice($errors, 0, 5) as $err) {
                $this->line("  • {$err}");
            }
        }

        $this->newLine();
        $this->info('🎉 Re-embedding selesai!');
        return self::SUCCESS;
    }
}
