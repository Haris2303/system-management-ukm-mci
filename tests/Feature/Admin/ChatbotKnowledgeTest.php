<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\RagDocuments\Pages\ListRagDocuments;
use App\Filament\Resources\RagDocuments\Pages\ViewRagDocument;
use App\Filament\Resources\RagDocuments\RagDocumentResource;
use App\Jobs\ProcessPdfJob;
use App\Models\RagChunk;
use App\Models\RagDocument;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ChatbotKnowledgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);

        Storage::fake('local');
        Queue::fake();
    }

    // ─── helpers ───────────────────────────────────────────────

    private function buatUser(string $role): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function buatDokumen(array $attrs = []): RagDocument
    {
        return RagDocument::create(array_merge([
            'nama_file'    => 'panduan-ukm.pdf',
            'path_file'    => 'rag-docs/panduan-ukm.pdf',
            'deskripsi'    => 'Panduan UKM MCI',
            'total_chunks' => 0,
            'status'       => 'ready',
        ], $attrs));
    }

    private function buatChunk(RagDocument $doc, int $index = 1, ?array $embedding = null): RagChunk
    {
        return RagChunk::create([
            'document_id' => $doc->id,
            'chunk_index' => $index,
            'content'     => 'Ini adalah isi chunk ke-' . $index . ' dari dokumen panduan.',
            'embedding'   => $embedding,
            'token_count' => 50,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — canViewAny RagDocumentResource
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_akses_knowledge_base(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(RagDocumentResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_akses_knowledge_base(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(RagDocumentResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_akses_knowledge_base(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(RagDocumentResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_akses_knowledge_base(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(RagDocumentResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_akses_knowledge_base(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(RagDocumentResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // LIST PAGE — aksi berdasarkan status dokumen
    // ══════════════════════════════════════════════════════════════

    public function test_list_page_dapat_diakses_ketua_ukm(): void
    {
        $admin = $this->buatUser('ketua_ukm');

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->assertSuccessful();
    }

    public function test_aksi_lihat_isi_tersedia_untuk_dokumen_ready(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'ready']);

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->assertTableActionVisible('lihat_isi', $dok);
    }

    public function test_aksi_lihat_isi_tersembunyi_untuk_dokumen_processing(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'processing']);

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->assertTableActionHidden('lihat_isi', $dok);
    }

    public function test_aksi_lihat_progress_tersedia_untuk_dokumen_processing(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'processing']);

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->assertTableActionVisible('lihat_progress', $dok);
    }

    public function test_aksi_lihat_progress_tersembunyi_untuk_dokumen_ready(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'ready']);

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->assertTableActionHidden('lihat_progress', $dok);
    }

    public function test_aksi_reprocess_tersedia_untuk_dokumen_error(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'error']);

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->assertTableActionVisible('reprocess', $dok);
    }

    public function test_aksi_reprocess_tersembunyi_untuk_dokumen_ready(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'ready']);

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->assertTableActionHidden('reprocess', $dok);
    }

    public function test_eksekusi_reprocess_menghapus_chunks_lama_dan_dispatch_job(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'error', 'total_chunks' => 5]);

        // Buat beberapa chunk lama
        $this->buatChunk($dok, 1);
        $this->buatChunk($dok, 2);
        $this->assertDatabaseCount('rag_chunks', 2);

        Livewire::actingAs($admin)
                ->test(ListRagDocuments::class)
                ->callTableAction('reprocess', $dok)
                ->assertHasNoTableActionErrors();

        // Chunks lama dihapus
        $this->assertDatabaseCount('rag_chunks', 0);

        // Status di-reset ke processing
        $fresh = $dok->fresh();
        $this->assertEquals('processing', $fresh->status);
        $this->assertEquals(0, $fresh->total_chunks);

        // Job dijadwalkan
        Queue::assertPushed(ProcessPdfJob::class, fn($job) => $job->documentId === $dok->id);
    }

    // ══════════════════════════════════════════════════════════════
    // VIEW PAGE — detail dokumen & chunks
    // ══════════════════════════════════════════════════════════════

    public function test_view_page_menampilkan_nama_dokumen(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['nama_file' => 'dokumen-penting.pdf']);

        Livewire::actingAs($admin)
                ->test(ViewRagDocument::class, ['record' => $dok->getKey()])
                ->assertSuccessful()
                ->assertSee('dokumen-penting.pdf');
    }

    public function test_view_page_menampilkan_chunks_dokumen(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        $dok   = $this->buatDokumen(['status' => 'ready']);

        $this->buatChunk($dok, 1);
        $this->buatChunk($dok, 2);
        $this->buatChunk($dok, 3);

        Livewire::actingAs($admin)
                ->test(ViewRagDocument::class, ['record' => $dok->getKey()])
                ->assertSuccessful()
                ->assertCountTableRecords(3);
    }

    // ══════════════════════════════════════════════════════════════
    // CHATBOT ROUTES — endpoint publik
    // ══════════════════════════════════════════════════════════════

    public function test_suggested_mengembalikan_daftar_pertanyaan(): void
    {
        $response = $this->getJson('/chatbot/suggested');

        $response->assertOk()
                 ->assertJsonStructure(['suggestions'])
                 ->assertJsonCount(6, 'suggestions');
    }

    public function test_status_mengembalikan_info_dokumen(): void
    {
        $dok = $this->buatDokumen([
            'status'       => 'ready',
            'total_chunks' => 10,
            'nama_file'    => 'panduan.pdf',
        ]);

        $this->getJson("/chatbot/status/{$dok->id}")
             ->assertOk()
             ->assertJsonPath('status', 'ready')
             ->assertJsonPath('total_chunks', 10)
             ->assertJsonPath('nama_file', 'panduan.pdf');
    }

    public function test_status_mengembalikan_404_untuk_dokumen_tidak_ada(): void
    {
        $this->getJson('/chatbot/status/99999')->assertNotFound();
    }

    public function test_upload_memerlukan_autentikasi(): void
    {
        $this->postJson('/chatbot/upload', [
            'pdf' => UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'),
        ])->assertUnauthorized();
    }

    public function test_upload_membuat_record_dokumen_dan_dispatch_job(): void
    {
        $user = $this->buatUser('ketua_ukm');

        $this->actingAs($user)
             ->postJson('/chatbot/upload', [
                 'pdf'       => UploadedFile::fake()->create('panduan.pdf', 500, 'application/pdf'),
                 'deskripsi' => 'Panduan anggota UKM MCI',
             ])
             ->assertStatus(202)
             ->assertJsonPath('status', 'processing')
             ->assertJsonStructure(['document_id']);

        $this->assertDatabaseHas('rag_documents', [
            'status'    => 'processing',
            'deskripsi' => 'Panduan anggota UKM MCI',
        ]);

        Queue::assertPushed(ProcessPdfJob::class);
    }

    public function test_upload_gagal_validasi_jika_bukan_pdf(): void
    {
        $user = $this->buatUser('ketua_ukm');

        $this->actingAs($user)
             ->postJson('/chatbot/upload', [
                 'pdf' => UploadedFile::fake()->create('gambar.jpg', 100, 'image/jpeg'),
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['pdf']);
    }

    public function test_upload_gagal_validasi_jika_tidak_ada_file(): void
    {
        $user = $this->buatUser('ketua_ukm');

        $this->actingAs($user)
             ->postJson('/chatbot/upload', [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['pdf']);
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL RagDocument
    // ══════════════════════════════════════════════════════════════

    public function test_rag_document_memiliki_relasi_ke_chunks(): void
    {
        $dok = $this->buatDokumen();
        $this->buatChunk($dok, 1);
        $this->buatChunk($dok, 2);

        $this->assertCount(2, $dok->chunks);
        $this->assertInstanceOf(RagChunk::class, $dok->chunks->first());
    }

    public function test_menghapus_dokumen_menghapus_semua_chunks(): void
    {
        $dok = $this->buatDokumen();
        $this->buatChunk($dok, 1);
        $this->buatChunk($dok, 2);

        $this->assertDatabaseCount('rag_chunks', 2);

        $dok->delete();

        $this->assertDatabaseCount('rag_chunks', 0);
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL RagChunk — cosineSimilarity
    // ══════════════════════════════════════════════════════════════

    public function test_cosine_similarity_identik_mengembalikan_1(): void
    {
        $dok   = $this->buatDokumen();
        $chunk = $this->buatChunk($dok, 1, [0.6, 0.8]);

        $similarity = $chunk->cosineSimilarity([0.6, 0.8]);

        $this->assertEqualsWithDelta(1.0, $similarity, 0.0001);
    }

    public function test_cosine_similarity_tegak_lurus_mengembalikan_0(): void
    {
        $dok   = $this->buatDokumen();
        $chunk = $this->buatChunk($dok, 1, [1.0, 0.0]);

        $similarity = $chunk->cosineSimilarity([0.0, 1.0]);

        $this->assertEqualsWithDelta(0.0, $similarity, 0.0001);
    }

    public function test_cosine_similarity_berlawanan_mengembalikan_negatif(): void
    {
        $dok   = $this->buatDokumen();
        $chunk = $this->buatChunk($dok, 1, [1.0, 0.0]);

        $similarity = $chunk->cosineSimilarity([-1.0, 0.0]);

        $this->assertEqualsWithDelta(-1.0, $similarity, 0.0001);
    }

    public function test_cosine_similarity_nol_jika_embedding_kosong(): void
    {
        $dok   = $this->buatDokumen();
        $chunk = $this->buatChunk($dok, 1, null);

        $similarity = $chunk->cosineSimilarity([0.5, 0.5]);

        $this->assertEquals(0.0, $similarity);
    }

    public function test_cosine_similarity_nol_jika_dimensi_tidak_cocok(): void
    {
        $dok   = $this->buatDokumen();
        $chunk = $this->buatChunk($dok, 1, [0.5, 0.5, 0.5]);

        // Query embedding berbeda dimensi
        $similarity = $chunk->cosineSimilarity([0.5, 0.5]);

        $this->assertEquals(0.0, $similarity);
    }

    // ══════════════════════════════════════════════════════════════
    // JOB ProcessPdfJob — logika splitting teks
    // ══════════════════════════════════════════════════════════════

    public function test_split_into_chunks_memisahkan_teks_panjang(): void
    {
        $job = new ProcessPdfJob(1);

        // Akses metode private via reflection
        $method = new \ReflectionMethod(ProcessPdfJob::class, 'splitIntoChunks');
        $method->setAccessible(true);

        $teks   = str_repeat('Ini adalah kalimat panjang untuk uji chunking. ', 30);
        $chunks = $method->invoke($job, $teks);

        $this->assertGreaterThan(1, count($chunks));
    }

    public function test_split_into_chunks_menghapus_chunk_terlalu_pendek(): void
    {
        $job = new ProcessPdfJob(1);

        $method = new \ReflectionMethod(ProcessPdfJob::class, 'splitIntoChunks');
        $method->setAccessible(true);

        // Teks sangat pendek (< 50 karakter) harus disaring
        $chunks = $method->invoke($job, 'Pendek.');

        $this->assertCount(0, $chunks);
    }

    public function test_split_into_chunks_menangani_whitespace_berlebih(): void
    {
        $job = new ProcessPdfJob(1);

        $method = new \ReflectionMethod(ProcessPdfJob::class, 'splitIntoChunks');
        $method->setAccessible(true);

        $teks   = "Ini   teks   dengan\n\nbanyak\t\tspasi. " . str_repeat('Kalimat normal. ', 20);
        $chunks = $method->invoke($job, $teks);

        $this->assertNotEmpty($chunks);
        // Whitespace berlebih harus dikolapskan
        foreach ($chunks as $chunk) {
            $this->assertStringNotContainsString('  ', $chunk);
        }
    }

    public function test_job_dispatch_ketika_upload_dokumen(): void
    {
        $user = $this->buatUser('ketua_ukm');

        $this->actingAs($user)
             ->postJson('/chatbot/upload', [
                 'pdf' => UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'),
             ])
             ->assertStatus(202);

        Queue::assertPushed(ProcessPdfJob::class);
        Queue::assertPushedOn(null, ProcessPdfJob::class); // default queue
    }
}
