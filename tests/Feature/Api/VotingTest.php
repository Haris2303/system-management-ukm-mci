<?php

namespace Tests\Feature\Api;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'anggota',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'demisioner', 'guard_name' => 'web']);
    }

    // ─── helpers ───────────────────────────────────────────────

    /** Election aktif dengan waktu valid (tidak akan di-auto-close oleh retrieved hook). */
    private function buatElection(array $attrs = []): Election
    {
        return Election::create(array_merge([
            'judul'           => 'Pemilihan Ketua UKM',
            'deskripsi'       => 'Pemilihan tahunan',
            'posisi'          => 'Ketua UKM',
            'status'          => 'aktif',
            'waktu_mulai'     => now()->subHour(),
            'waktu_selesai'   => now()->addHours(2),
            'is_anonim'       => true,
            'tampil_realtime' => false,
            'created_by'      => User::factory()->create()->id,
        ], $attrs));
    }

    private function buatKandidat(Election $election, User $user, array $attrs = []): Candidate
    {
        return Candidate::create(array_merge([
            'election_id' => $election->id,
            'user_id'     => $user->id,
            'visi'        => 'Visi kandidat',
            'misi'        => 'Misi kandidat',
        ], $attrs));
    }

    private function buatSuara(Election $election, Candidate $candidate, User $voter): Vote
    {
        return Vote::create([
            'election_id'  => $election->id,
            'candidate_id' => $candidate->id,
            'voter_hash'   => hash('sha256', $voter->id . '-' . $election->id . '-ukm-mci-secret'),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // DAFTAR ELECTION — GET /api/elections
    // ══════════════════════════════════════════════════════════════

    public function test_index_mengembalikan_election_aktif_selesai_dan_tie(): void
    {
        $this->buatElection(['status' => 'aktif']);
        $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);
        $this->buatElection(['status' => 'tie',     'waktu_selesai' => now()->subHour()]);

        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/elections')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_index_tidak_mengembalikan_election_draft(): void
    {
        $this->buatElection(['status' => 'draft']);
        $this->buatElection(['status' => 'aktif']);

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/elections')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('aktif', $response->json('data.0.status'));
    }

    public function test_index_diurutkan_berdasarkan_waktu_mulai_terbaru(): void
    {
        $lama  = $this->buatElection(['judul' => 'Election Lama', 'waktu_mulai' => now()->subDays(2)]);
        $baru  = $this->buatElection(['judul' => 'Election Baru', 'waktu_mulai' => now()->subHour()]);

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/elections')
            ->assertOk();

        $this->assertEquals($baru->id,  $response->json('data.0.id'));
        $this->assertEquals($lama->id,  $response->json('data.1.id'));
    }

    public function test_index_menampilkan_sudah_vote_true_jika_user_sudah_memilih(): void
    {
        $election  = $this->buatElection();
        $voter     = $this->aktifUser();
        $kandidat  = $this->buatKandidat($election, $this->aktifUser());

        $this->buatSuara($election, $kandidat, $voter);

        $response = $this->withToken($this->tokenFor($voter))
            ->getJson('/api/elections')
            ->assertOk();

        $this->assertTrue($response->json('data.0.sudah_vote'));
    }

    public function test_index_menampilkan_sudah_vote_false_jika_belum_memilih(): void
    {
        $this->buatElection();
        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/elections')
            ->assertOk();

        $this->assertFalse($response->json('data.0.sudah_vote'));
    }

    public function test_index_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/elections')->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // DETAIL ELECTION — GET /api/elections/{id}
    // ══════════════════════════════════════════════════════════════

    public function test_show_mengembalikan_detail_election_beserta_kandidat(): void
    {
        $election = $this->buatElection();
        $this->buatKandidat($election, $this->aktifUser());
        $this->buatKandidat($election, $this->aktifUser());

        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $election->id)
            ->assertJsonPath('data.judul', $election->judul)
            ->assertJsonCount(2, 'data.kandidat');
    }

    public function test_show_mengembalikan_404_untuk_election_tidak_ada(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/elections/99999')
            ->assertNotFound();
    }

    public function test_show_menyertakan_jumlah_suara_jika_realtime_aktif(): void
    {
        $election  = $this->buatElection(['tampil_realtime' => true, 'status' => 'aktif']);
        $kandidat  = $this->buatKandidat($election, $this->aktifUser());
        $voter     = $this->aktifUser();
        $this->buatSuara($election, $kandidat, $voter);

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}")
            ->assertOk();

        $this->assertArrayHasKey('jumlah_suara', $response->json('data.kandidat.0'));
    }

    public function test_show_menyembunyikan_jumlah_suara_jika_realtime_nonaktif_dan_masih_aktif(): void
    {
        $election = $this->buatElection(['tampil_realtime' => false, 'status' => 'aktif']);
        $this->buatKandidat($election, $this->aktifUser());

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}")
            ->assertOk();

        $this->assertArrayNotHasKey('jumlah_suara', $response->json('data.kandidat.0'));
    }

    public function test_show_memerlukan_autentikasi(): void
    {
        $election = $this->buatElection();
        $this->getJson("/api/elections/{$election->id}")->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // KIRIM SUARA — POST /api/elections/{id}/vote
    // ══════════════════════════════════════════════════════════════

    public function test_vote_berhasil_dicatat(): void
    {
        $election = $this->buatElection();
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertCreated()
            ->assertJsonPath('status', 'berhasil');
    }

    public function test_vote_tersimpan_di_database_secara_anonim(): void
    {
        $election = $this->buatElection(['is_anonim' => true]);
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertCreated();

        $expectedHash = hash('sha256', $voter->id . '-' . $election->id . '-ukm-mci-secret');

        $this->assertDatabaseHas('votes', [
            'election_id'  => $election->id,
            'candidate_id' => $kandidat->id,
            'voter_hash'   => $expectedHash,
        ]);

        // Pastikan tidak ada kolom user_id di tabel votes (benar-benar anonim)
        $vote = Vote::first();
        $this->assertArrayNotHasKey('user_id', $vote->toArray());
    }

    public function test_vote_menambah_jumlah_suara_kandidat(): void
    {
        $election = $this->buatElection();
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->assertEquals(0, $kandidat->jumlahSuara());

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertCreated();

        $this->assertEquals(1, $kandidat->fresh()->jumlahSuara());
    }

    // ── Status election ─────────────────────────────────────────

    public function test_vote_gagal_jika_election_masih_draft(): void
    {
        $election = $this->buatElection(['status' => 'draft']);
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertStatus(400)
            ->assertJsonFragment(['pesan' => 'Pemilihan belum dibuka. Silakan tunggu hingga waktu voting dimulai.']);
    }

    public function test_vote_gagal_jika_election_sudah_selesai(): void
    {
        $election = $this->buatElection([
            'status'        => 'selesai',
            'waktu_selesai' => now()->subHour(),
        ]);
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertStatus(400)
            ->assertJsonFragment(['pesan' => 'Pemilihan sudah ditutup. Terima kasih atas partisipasi Anda.']);
    }

    public function test_vote_gagal_jika_election_dalam_status_tie(): void
    {
        $election = $this->buatElection([
            'status'        => 'tie',
            'waktu_selesai' => now()->subHour(),
        ]);
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertStatus(400)
            ->assertJsonFragment(['pesan' => 'Pemilihan sedang dalam proses musyawarah. Voting reguler ditutup sementara.']);
    }

    // ── Validasi waktu ──────────────────────────────────────────

    public function test_vote_gagal_jika_belum_waktu_mulai(): void
    {
        $election = $this->buatElection([
            'waktu_mulai'   => now()->addHour(),
            'waktu_selesai' => now()->addHours(3),
        ]);
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertStatus(400)
            ->assertJsonFragment(['pesan' => 'Pemilihan belum dimulai.']);
    }

    // ── Kandidat ────────────────────────────────────────────────

    public function test_vote_gagal_jika_kandidat_tidak_ada_di_election_ini(): void
    {
        $election       = $this->buatElection();
        $electionLain   = $this->buatElection();
        $kandidatAsing  = $this->buatKandidat($electionLain, $this->aktifUser());
        $voter          = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidatAsing->id,
            ])
            ->assertNotFound()
            ->assertJsonFragment(['pesan' => 'Kandidat tidak ditemukan dalam pemilihan ini.']);
    }

    // ── Duplikasi ───────────────────────────────────────────────

    public function test_vote_gagal_jika_user_sudah_memilih_sebelumnya(): void
    {
        $election = $this->buatElection();
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        // Suara pertama
        $this->buatSuara($election, $kandidat, $voter);

        // Suara kedua harus ditolak
        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [
                'candidate_id' => $kandidat->id,
            ])
            ->assertUnprocessable()
            ->assertJsonFragment(['pesan' => 'Anda sudah memberikan suara pada pemilihan ini. Satu anggota hanya boleh memilih satu kali.']);
    }

    public function test_user_berbeda_dapat_memilih_di_election_yang_sama(): void
    {
        $election  = $this->buatElection();
        $kandidat  = $this->buatKandidat($election, $this->aktifUser());
        $voter1    = $this->aktifUser();
        $voter2    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter1))
            ->postJson("/api/elections/{$election->id}/vote", ['candidate_id' => $kandidat->id])
            ->assertCreated();

        $this->app['auth']->forgetGuards();

        $this->withToken($this->tokenFor($voter2))
            ->postJson("/api/elections/{$election->id}/vote", ['candidate_id' => $kandidat->id])
            ->assertCreated();

        $this->assertDatabaseCount('votes', 2);
    }

    // ── Validasi input ──────────────────────────────────────────

    public function test_vote_gagal_jika_candidate_id_tidak_diisi(): void
    {
        $election = $this->buatElection();
        $voter    = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson("/api/elections/{$election->id}/vote", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['candidate_id']);
    }

    public function test_vote_gagal_untuk_election_yang_tidak_ada(): void
    {
        $voter = $this->aktifUser();

        $this->withToken($this->tokenFor($voter))
            ->postJson('/api/elections/99999/vote', ['candidate_id' => 1])
            ->assertNotFound();
    }

    // ── Hak akses ───────────────────────────────────────────────

    public function test_vote_memerlukan_autentikasi(): void
    {
        $election = $this->buatElection();

        $this->postJson("/api/elections/{$election->id}/vote", ['candidate_id' => 1])
            ->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // HASIL VOTING — GET /api/elections/{id}/hasil
    // ══════════════════════════════════════════════════════════════

    public function test_hasil_ditampilkan_saat_election_selesai(): void
    {
        $election = $this->buatElection([
            'status'        => 'selesai',
            'waktu_selesai' => now()->subHour(),
        ]);
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk()
            ->assertJsonPath('pesan', 'Hasil pemilihan berhasil dimuat.');
    }

    public function test_hasil_ditampilkan_saat_realtime_aktif_meskipun_election_masih_aktif(): void
    {
        $election = $this->buatElection([
            'status'          => 'aktif',
            'tampil_realtime' => true,
        ]);
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk();
    }

    public function test_hasil_diblokir_saat_election_aktif_tanpa_realtime(): void
    {
        $election = $this->buatElection([
            'status'          => 'aktif',
            'tampil_realtime' => false,
        ]);
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertForbidden();
    }

    public function test_hasil_diblokir_saat_election_masih_draft(): void
    {
        $election = $this->buatElection(['status' => 'draft', 'tampil_realtime' => false]);
        $user     = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertForbidden();
    }

    public function test_hasil_menampilkan_pemenang_dengan_suara_terbanyak(): void
    {
        $election   = $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);
        $kandidat1  = $this->buatKandidat($election, $this->aktifUser());
        $kandidat2  = $this->buatKandidat($election, $this->aktifUser());

        // kandidat1 dapat 2 suara, kandidat2 dapat 1 suara
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat2, $this->aktifUser());

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk();

        $this->assertEquals($kandidat1->id, $response->json('data.pemenang.id'));
        $this->assertEquals(3, $response->json('data.total_suara'));
    }

    public function test_hasil_kandidat_diurutkan_dari_suara_terbanyak(): void
    {
        $election  = $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->aktifUser());
        $kandidat2 = $this->buatKandidat($election, $this->aktifUser());

        // kandidat2 menang
        $this->buatSuara($election, $kandidat2, $this->aktifUser());
        $this->buatSuara($election, $kandidat2, $this->aktifUser());
        $this->buatSuara($election, $kandidat1, $this->aktifUser());

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk();

        $kandidat = $response->json('data.kandidat');
        $this->assertEquals($kandidat2->id, $kandidat[0]['id']);
        $this->assertEquals(1, $kandidat[0]['peringkat']);
        $this->assertEquals($kandidat1->id, $kandidat[1]['id']);
        $this->assertEquals(2, $kandidat[1]['peringkat']);
    }

    public function test_hasil_persentase_dihitung_dengan_benar(): void
    {
        $election  = $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->aktifUser());
        $kandidat2 = $this->buatKandidat($election, $this->aktifUser());

        // 3 suara untuk kandidat1, 1 untuk kandidat2 → 75% vs 25%
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat2, $this->aktifUser());

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk();

        $kandidat = collect($response->json('data.kandidat'));
        $this->assertEquals(75.0, $kandidat->firstWhere('id', $kandidat1->id)['persentase']);
        $this->assertEquals(25.0, $kandidat->firstWhere('id', $kandidat2->id)['persentase']);
    }

    // ── Skenario TIE ────────────────────────────────────────────

    public function test_hasil_pemenang_null_saat_status_tie_belum_diresolusi(): void
    {
        $election  = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->aktifUser());
        $kandidat2 = $this->buatKandidat($election, $this->aktifUser());

        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat2, $this->aktifUser());

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk();

        $this->assertNull($response->json('data.pemenang'));
        $this->assertTrue($response->json('data.is_tie'));
        $this->assertNotNull($response->json('data.catatan'));
    }

    public function test_hasil_pemenang_null_saat_tie_resolution_type_revote(): void
    {
        $election = $this->buatElection([
            'status'              => 'selesai',
            'tie_resolution_type' => 'revote',
            'tie_resolved_at'     => now()->subMinutes(10),
            'waktu_selesai'       => now()->subHour(),
        ]);
        $this->buatKandidat($election, $this->aktifUser());

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk();

        $this->assertNull($response->json('data.pemenang'));
        $this->assertStringContainsString('Putaran Kedua', $response->json('data.catatan'));
    }

    public function test_hasil_pemenang_adalah_kandidat_musyawarah_saat_tie_deliberation(): void
    {
        $election  = $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->aktifUser());
        $kandidat2 = $this->buatKandidat($election, $this->aktifUser());

        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat2, $this->aktifUser());

        // Presidium menetapkan kandidat1 sebagai pemenang
        $election->update([
            'tie_resolution_type'     => 'deliberation',
            'tie_winner_candidate_id' => $kandidat1->id,
            'tie_resolved_at'         => now(),
        ]);

        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/elections/{$election->id}/hasil")
            ->assertOk();

        $this->assertEquals($kandidat1->id, $response->json('data.pemenang.id'));
        $this->assertStringContainsString('musyawarah', $response->json('data.catatan'));
    }

    public function test_hasil_memerlukan_autentikasi(): void
    {
        $election = $this->buatElection(['status' => 'selesai']);
        $this->getJson("/api/elections/{$election->id}/hasil")->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // PERILAKU MODEL — Election & detectAndHandleTie
    // ══════════════════════════════════════════════════════════════

    public function test_detect_tie_mengubah_status_ke_tie_jika_seri(): void
    {
        $election  = $this->buatElection(['status' => 'selesai']);
        $kandidat1 = $this->buatKandidat($election, $this->aktifUser());
        $kandidat2 = $this->buatKandidat($election, $this->aktifUser());

        // Masing-masing 1 suara → seri
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat2, $this->aktifUser());

        $result = $election->detectAndHandleTie();

        $this->assertTrue($result);
        $this->assertEquals('tie', $election->fresh()->status);
    }

    public function test_detect_tie_tidak_mengubah_status_jika_ada_pemenang_jelas(): void
    {
        $election  = $this->buatElection(['status' => 'selesai']);
        $kandidat1 = $this->buatKandidat($election, $this->aktifUser());
        $kandidat2 = $this->buatKandidat($election, $this->aktifUser());

        // kandidat1 unggul
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat1, $this->aktifUser());
        $this->buatSuara($election, $kandidat2, $this->aktifUser());

        $result = $election->detectAndHandleTie();

        $this->assertFalse($result);
        $this->assertEquals('selesai', $election->fresh()->status);
    }

    public function test_detect_tie_mengembalikan_false_jika_belum_ada_suara(): void
    {
        $election = $this->buatElection(['status' => 'selesai']);
        $this->buatKandidat($election, $this->aktifUser());
        $this->buatKandidat($election, $this->aktifUser());

        $result = $election->detectAndHandleTie();

        $this->assertFalse($result);
        $this->assertEquals('selesai', $election->fresh()->status);
    }

    public function test_election_otomatis_ditutup_saat_diambil_setelah_waktu_selesai(): void
    {
        $election = Election::create([
            'judul'           => 'Election Kadaluarsa',
            'deskripsi'       => 'Test',
            'posisi'          => 'Ketua',
            'status'          => 'aktif',
            'waktu_mulai'     => now()->subHours(3),
            'waktu_selesai'   => now()->subHour(),
            'is_anonim'       => true,
            'tampil_realtime' => false,
            'created_by'      => User::factory()->create()->id,
        ]);

        // Re-fetch memicu retrieved hook → auto-close
        $fresh = Election::find($election->id);

        $this->assertNotEquals('aktif', $fresh->status);
    }

    public function test_sudah_divote_mendeteksi_user_yang_sudah_memilih(): void
    {
        $election = $this->buatElection();
        $kandidat = $this->buatKandidat($election, $this->aktifUser());
        $voter    = $this->aktifUser();

        $this->assertFalse($election->sudahDivote($voter->id));

        $this->buatSuara($election, $kandidat, $voter);

        $this->assertTrue($election->sudahDivote($voter->id));
    }

    public function test_sudah_divote_tidak_terpengaruh_oleh_vote_di_election_lain(): void
    {
        $election1 = $this->buatElection();
        $election2 = $this->buatElection();
        $kandidat  = $this->buatKandidat($election1, $this->aktifUser());
        $voter     = $this->aktifUser();

        $this->buatSuara($election1, $kandidat, $voter);

        // Sudah vote di election1 tapi BELUM di election2
        $this->assertFalse($election2->sudahDivote($voter->id));
    }
}
