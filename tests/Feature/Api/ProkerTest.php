<?php

namespace Tests\Feature\Api;

use App\Models\Divisi;
use App\Models\ProgramKerja;
use App\Models\TugasProker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProkerTest extends TestCase
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

    private function buatDivisi(string $nama = 'Web Dev'): Divisi
    {
        static $urut = 1;
        return Divisi::create([
            'nama'      => $nama,
            'slug'      => \Illuminate\Support\Str::slug($nama . '-' . $urut++),
            'icon'      => '💻',
            'is_active' => true,
            'urut'      => $urut,
        ]);
    }

    private function userDenganDivisi(Divisi $divisi): User
    {
        /** @var User $user */
        $user = User::factory()->create(['divisi_id' => $divisi->id]);
        $user->assignRole('anggota');
        return $user;
    }

    private function userTanpaDivisi(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['divisi_id' => null]);
        $user->assignRole('anggota');
        return $user;
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('test')->plainTextToken;
    }

    /** Buat proker umum (divisi_id = null). */
    private function buatProkerUmum(array $attrs = []): ProgramKerja
    {
        return ProgramKerja::create(array_merge([
            'divisi_id'       => null,
            'nama_proker'     => 'Proker Umum',
            'deskripsi'       => 'Deskripsi proker umum',
            'tanggal_mulai'   => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->addDays(20)->toDateString(),
            'status'          => 'active',
            'progress_persen' => 0,
        ], $attrs));
    }

    /** Buat proker milik divisi tertentu. */
    private function buatProkerDivisi(Divisi $divisi, array $attrs = []): ProgramKerja
    {
        return ProgramKerja::create(array_merge([
            'divisi_id'       => $divisi->id,
            'nama_proker'     => 'Proker Divisi ' . $divisi->nama,
            'deskripsi'       => 'Deskripsi',
            'tanggal_mulai'   => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(25)->toDateString(),
            'status'          => 'planning',
            'progress_persen' => 0,
        ], $attrs));
    }

    private function buatTugas(ProgramKerja $proker, string $nama = 'Tugas', bool $selesai = false): TugasProker
    {
        return TugasProker::create([
            'proker_id'  => $proker->id,
            'nama_tugas' => $nama,
            'is_selesai' => $selesai,
            'urut'       => TugasProker::where('proker_id', $proker->id)->max('urut') + 1,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // DAFTAR PROKER — GET /api/proker
    // ══════════════════════════════════════════════════════════════

    public function test_index_mengembalikan_proker_umum_untuk_semua_user(): void
    {
        $this->buatProkerUmum(['nama_proker' => 'Proker Umum 1']);
        $this->buatProkerUmum(['nama_proker' => 'Proker Umum 2']);

        $user = $this->userTanpaDivisi();

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        $this->assertCount(2, $response->json('data.proker'));
    }

    public function test_index_mengembalikan_proker_umum_dan_divisi_user(): void
    {
        $divisi = $this->buatDivisi('Web Dev');
        $user   = $this->userDenganDivisi($divisi);

        $this->buatProkerUmum();
        $this->buatProkerDivisi($divisi);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        // Menerima 2: proker umum + proker divisinya
        $this->assertCount(2, $response->json('data.proker'));
    }

    public function test_index_tidak_mengembalikan_proker_divisi_lain(): void
    {
        $divisiA = $this->buatDivisi('Web Dev');
        $divisiB = $this->buatDivisi('Mobile Dev');
        $userA   = $this->userDenganDivisi($divisiA);

        $this->buatProkerDivisi($divisiA, ['nama_proker' => 'Proker A']);
        $this->buatProkerDivisi($divisiB, ['nama_proker' => 'Proker B']);

        $response = $this->withToken($this->tokenFor($userA))
                         ->getJson('/api/proker')
                         ->assertOk();

        $this->assertCount(1, $response->json('data.proker'));
        $this->assertEquals('Proker A', $response->json('data.proker.0.nama_proker'));
    }

    public function test_index_user_tanpa_divisi_hanya_melihat_proker_umum(): void
    {
        $divisi = $this->buatDivisi();
        $user   = $this->userTanpaDivisi();

        $this->buatProkerUmum(['nama_proker' => 'Umum']);
        $this->buatProkerDivisi($divisi, ['nama_proker' => 'Khusus Divisi']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        $this->assertCount(1, $response->json('data.proker'));
        $this->assertEquals('Umum', $response->json('data.proker.0.nama_proker'));
    }

    public function test_index_statistik_dihitung_dengan_benar(): void
    {
        $user = $this->userTanpaDivisi();

        $this->buatProkerUmum(['status' => 'planning',   'progress_persen' => 0]);
        $this->buatProkerUmum(['status' => 'active',     'progress_persen' => 50]);
        $this->buatProkerUmum(['status' => 'completed',  'progress_persen' => 100]);
        // Proker terlambat: lewat deadline, progress < 100
        $this->buatProkerUmum([
            'status'          => 'active',
            'progress_persen' => 30,
            'tanggal_selesai' => now()->subDay()->toDateString(),
        ]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        $stats = $response->json('data.statistik');
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(1, $stats['planning']);
        $this->assertEquals(2, $stats['active']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(1, $stats['terlambat']);
    }

    public function test_index_field_data_lengkap(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatProkerUmum(['nama_proker' => 'Proker Test']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        $proker = $response->json('data.proker.0');
        $this->assertArrayHasKey('id', $proker);
        $this->assertArrayHasKey('nama_proker', $proker);
        $this->assertArrayHasKey('status', $proker);
        $this->assertArrayHasKey('label_status', $proker);
        $this->assertArrayHasKey('progress_persen', $proker);
        $this->assertArrayHasKey('is_terlambat', $proker);
        $this->assertArrayHasKey('sisa_hari', $proker);
        $this->assertArrayHasKey('sisa_hari_label', $proker);
        $this->assertArrayHasKey('total_tugas', $proker);
        $this->assertArrayHasKey('tugas_selesai', $proker);
    }

    public function test_index_flag_is_umum_benar(): void
    {
        $divisi = $this->buatDivisi();
        $user   = $this->userDenganDivisi($divisi);

        $this->buatProkerUmum();
        $this->buatProkerDivisi($divisi);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        $prokers = collect($response->json('data.proker'));
        $umum    = $prokers->firstWhere('is_umum', true);
        $divisiProker = $prokers->firstWhere('is_umum', false);

        $this->assertNotNull($umum);
        $this->assertNotNull($divisiProker);
    }

    public function test_index_pesan_kosong_jika_tidak_ada_proker(): void
    {
        $user = $this->userTanpaDivisi();

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        $this->assertStringContainsString('Belum ada', $response->json('pesan'));
        $this->assertCount(0, $response->json('data.proker'));
    }

    public function test_index_pesan_memuat_jumlah_proker(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatProkerUmum();
        $this->buatProkerUmum(['nama_proker' => 'Proker 2']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/proker')
                         ->assertOk();

        $this->assertStringContainsString('2', $response->json('pesan'));
    }

    // ── Hak Akses: Index ────────────────────────────────────────

    public function test_index_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/proker')->assertUnauthorized();
    }

    public function test_index_diblokir_untuk_demisioner(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('demisioner');

        $this->withToken($this->tokenFor($user))
             ->getJson('/api/proker')
             ->assertForbidden()
             ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // DETAIL PROKER — GET /api/proker/{id}
    // ══════════════════════════════════════════════════════════════

    public function test_show_mengembalikan_detail_proker_dengan_tugas(): void
    {
        $user  = $this->userTanpaDivisi();
        $proker = $this->buatProkerUmum();
        $this->buatTugas($proker, 'Tugas A');
        $this->buatTugas($proker, 'Tugas B');

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson("/api/proker/{$proker->id}")
                         ->assertOk();

        $this->assertEquals($proker->id, $response->json('data.id'));
        $this->assertCount(2, $response->json('data.tugas'));
    }

    public function test_show_proker_umum_bisa_diakses_oleh_semua_user(): void
    {
        $divisiB = $this->buatDivisi('B');
        $userB   = $this->userDenganDivisi($divisiB);
        $proker  = $this->buatProkerUmum();

        $this->withToken($this->tokenFor($userB))
             ->getJson("/api/proker/{$proker->id}")
             ->assertOk();
    }

    public function test_show_proker_divisi_lain_mengembalikan_404(): void
    {
        $divisiA = $this->buatDivisi('A');
        $divisiB = $this->buatDivisi('B');
        $userA   = $this->userDenganDivisi($divisiA);
        $proker  = $this->buatProkerDivisi($divisiB);

        $this->withToken($this->tokenFor($userA))
             ->getJson("/api/proker/{$proker->id}")
             ->assertNotFound()
             ->assertJsonFragment(['pesan' => 'Program kerja tidak ditemukan atau Anda tidak punya akses.']);
    }

    public function test_show_mengembalikan_404_untuk_proker_tidak_ada(): void
    {
        $user = $this->userTanpaDivisi();

        $this->withToken($this->tokenFor($user))
             ->getJson('/api/proker/99999')
             ->assertNotFound();
    }

    public function test_show_tugas_diurutkan_berdasarkan_urut(): void
    {
        $user   = $this->userTanpaDivisi();
        $proker = $this->buatProkerUmum();

        TugasProker::create(['proker_id' => $proker->id, 'nama_tugas' => 'Tugas Ketiga', 'urut' => 3]);
        TugasProker::create(['proker_id' => $proker->id, 'nama_tugas' => 'Tugas Pertama', 'urut' => 1]);
        TugasProker::create(['proker_id' => $proker->id, 'nama_tugas' => 'Tugas Kedua', 'urut' => 2]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson("/api/proker/{$proker->id}")
                         ->assertOk();

        $tugas = $response->json('data.tugas');
        $this->assertEquals('Tugas Pertama', $tugas[0]['nama_tugas']);
        $this->assertEquals('Tugas Kedua',   $tugas[1]['nama_tugas']);
        $this->assertEquals('Tugas Ketiga',  $tugas[2]['nama_tugas']);
    }

    public function test_show_menyertakan_data_divisi_jika_proker_divisi(): void
    {
        $divisi = $this->buatDivisi('Web Dev');
        $user   = $this->userDenganDivisi($divisi);
        $proker = $this->buatProkerDivisi($divisi);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson("/api/proker/{$proker->id}")
                         ->assertOk();

        $this->assertNotNull($response->json('data.divisi'));
        $this->assertEquals($divisi->id, $response->json('data.divisi.id'));
    }

    public function test_show_divisi_null_untuk_proker_umum(): void
    {
        $user   = $this->userTanpaDivisi();
        $proker = $this->buatProkerUmum();

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson("/api/proker/{$proker->id}")
                         ->assertOk();

        $this->assertNull($response->json('data.divisi'));
    }

    public function test_show_menyertakan_data_pic_jika_ada(): void
    {
        $pic    = User::factory()->create(['name' => 'Koordinator Proker']);
        $user   = $this->userTanpaDivisi();
        $proker = $this->buatProkerUmum(['pic_id' => $pic->id]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson("/api/proker/{$proker->id}")
                         ->assertOk();

        $this->assertNotNull($response->json('data.pic'));
        $this->assertEquals('Koordinator Proker', $response->json('data.pic.name'));
    }

    // ── Hak Akses: Show ─────────────────────────────────────────

    public function test_show_memerlukan_autentikasi(): void
    {
        $proker = $this->buatProkerUmum();
        $this->getJson("/api/proker/{$proker->id}")->assertUnauthorized();
    }

    public function test_show_diblokir_untuk_demisioner(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('demisioner');
        $proker = $this->buatProkerUmum();

        $this->withToken($this->tokenFor($user))
             ->getJson("/api/proker/{$proker->id}")
             ->assertForbidden()
             ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL — isOverdue, sisaHari, progress
    // ══════════════════════════════════════════════════════════════

    public function test_is_overdue_true_jika_lewat_deadline_dan_belum_selesai(): void
    {
        $proker = $this->buatProkerUmum([
            'tanggal_selesai' => now()->subDay()->toDateString(),
            'progress_persen' => 50,
        ]);
        $this->assertTrue($proker->isOverdue());
    }

    public function test_is_overdue_false_jika_progress_100_meski_lewat_deadline(): void
    {
        $proker = $this->buatProkerUmum([
            'tanggal_selesai' => now()->subDay()->toDateString(),
            'progress_persen' => 100,
        ]);
        $this->assertFalse($proker->isOverdue());
    }

    public function test_is_overdue_false_jika_deadline_masih_besok(): void
    {
        $proker = $this->buatProkerUmum([
            'tanggal_selesai' => now()->addDay()->toDateString(),
            'progress_persen' => 0,
        ]);
        $this->assertFalse($proker->isOverdue());
    }

    public function test_sisa_hari_positif_jika_deadline_masih_akan_datang(): void
    {
        $proker = $this->buatProkerUmum([
            'tanggal_selesai' => now()->addDays(10)->toDateString(),
        ]);
        // diffInDays truncates, so 10 days from now may return 9 or 10
        $this->assertGreaterThanOrEqual(9, $proker->sisaHari());
        $this->assertLessThanOrEqual(10, $proker->sisaHari());
    }

    public function test_sisa_hari_negatif_jika_sudah_lewat_deadline(): void
    {
        $proker = $this->buatProkerUmum([
            'tanggal_selesai' => now()->subDays(5)->toDateString(),
        ]);
        $this->assertEquals(-5, $proker->sisaHari());
    }

    public function test_calculate_progress_nol_jika_tidak_ada_tugas(): void
    {
        $proker = $this->buatProkerUmum();
        $this->assertEquals(0, $proker->calculateProgress());
    }

    public function test_calculate_progress_50_jika_setengah_tugas_selesai(): void
    {
        $proker = $this->buatProkerUmum();
        $this->buatTugas($proker, 'Selesai', selesai: true);
        $this->buatTugas($proker, 'Belum');

        $this->assertEquals(50, $proker->calculateProgress());
    }

    public function test_calculate_progress_100_jika_semua_tugas_selesai(): void
    {
        $proker = $this->buatProkerUmum();
        $this->buatTugas($proker, 'Tugas 1', selesai: true);
        $this->buatTugas($proker, 'Tugas 2', selesai: true);

        $this->assertEquals(100, $proker->calculateProgress());
    }

    public function test_update_progress_mengubah_kolom_progress_persen(): void
    {
        $proker = $this->buatProkerUmum(['progress_persen' => 0]);
        $this->buatTugas($proker, 'Selesai', selesai: true);
        $this->buatTugas($proker, 'Belum');

        $proker->updateProgress();

        $this->assertEquals(50, $proker->fresh()->progress_persen);
    }

    public function test_update_progress_auto_ubah_status_ke_completed_jika_100(): void
    {
        $proker = $this->buatProkerUmum(['status' => 'active']);
        $this->buatTugas($proker, 'Tugas 1', selesai: true);
        $this->buatTugas($proker, 'Tugas 2', selesai: true);

        $proker->updateProgress();

        $this->assertEquals('completed', $proker->fresh()->status);
        $this->assertEquals(100, $proker->fresh()->progress_persen);
    }

    public function test_update_progress_ubah_status_ke_active_jika_lebih_dari_0(): void
    {
        $proker = $this->buatProkerUmum(['status' => 'planning', 'progress_persen' => 0]);
        $this->buatTugas($proker, 'Selesai', selesai: true);
        $this->buatTugas($proker, 'Belum');

        $proker->updateProgress();

        $this->assertEquals('active', $proker->fresh()->status);
    }

    public function test_label_status_sesuai_enum(): void
    {
        $planning  = $this->buatProkerUmum(['status' => 'planning']);
        $active    = $this->buatProkerUmum(['status' => 'active',    'progress_persen' => 50]);
        $completed = $this->buatProkerUmum(['status' => 'completed', 'progress_persen' => 100]);

        $this->assertStringContainsString('Perencanaan', $planning->label_status);
        $this->assertStringContainsString('Berjalan',    $active->label_status);
        $this->assertStringContainsString('Selesai',     $completed->label_status);
    }

    public function test_warna_progress_success_jika_100(): void
    {
        $proker = $this->buatProkerUmum(['progress_persen' => 100]);
        $this->assertEquals('success', $proker->warna_progress);
    }

    public function test_warna_progress_danger_jika_terlambat(): void
    {
        $proker = $this->buatProkerUmum([
            'progress_persen' => 30,
            'tanggal_selesai' => now()->subDay()->toDateString(),
        ]);
        $this->assertEquals('danger', $proker->warna_progress);
    }

    public function test_warna_progress_warning_jika_50_persen(): void
    {
        $proker = $this->buatProkerUmum([
            'progress_persen' => 50,
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
        ]);
        $this->assertEquals('warning', $proker->warna_progress);
    }

    public function test_warna_progress_info_jika_kurang_dari_50(): void
    {
        $proker = $this->buatProkerUmum([
            'progress_persen' => 30,
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
        ]);
        $this->assertEquals('info', $proker->warna_progress);
    }

    // ══════════════════════════════════════════════════════════════
    // OBSERVER — TugasProkerObserver
    // ══════════════════════════════════════════════════════════════

    public function test_observer_update_progress_saat_tugas_baru_ditambahkan(): void
    {
        $proker = $this->buatProkerUmum(['status' => 'planning', 'progress_persen' => 0]);

        // Tambah 1 tugas selesai → progress 100% → status completed
        TugasProker::create([
            'proker_id'  => $proker->id,
            'nama_tugas' => 'Satu-satunya Tugas',
            'is_selesai' => true,
            'urut'       => 1,
        ]);

        $this->assertEquals(100, $proker->fresh()->progress_persen);
        $this->assertEquals('completed', $proker->fresh()->status);
    }

    public function test_observer_update_progress_saat_tugas_diselesaikan(): void
    {
        $proker = $this->buatProkerUmum(['progress_persen' => 0]);
        $tugas  = $this->buatTugas($proker, 'Tugas', selesai: false);
        $this->buatTugas($proker, 'Tugas Lain', selesai: false);

        $this->assertEquals(0, $proker->fresh()->progress_persen);

        // Tandai satu tugas selesai
        $tugas->update(['is_selesai' => true]);

        $this->assertEquals(50, $proker->fresh()->progress_persen);
    }

    public function test_observer_update_progress_saat_tugas_dihapus(): void
    {
        $proker = $this->buatProkerUmum();
        $this->buatTugas($proker, 'Tugas 1', selesai: true);
        $tugasBelum = $this->buatTugas($proker, 'Tugas 2', selesai: false);

        // Observer sudah jalan saat tugas dibuat → 50%
        $this->assertEquals(50, $proker->fresh()->progress_persen);

        // Hapus tugas yang belum selesai → observer fired → hanya tugas1 → 100%
        $tugasBelum->delete();

        $this->assertEquals(100, $proker->fresh()->progress_persen);
    }

    public function test_observer_tidak_update_progress_jika_bukan_is_selesai_yang_berubah(): void
    {
        $proker = $this->buatProkerUmum();

        // Setup: 1 selesai + 1 belum → progress = 50%
        $this->buatTugas($proker, 'Tugas Selesai', selesai: true);
        $tugasBelum = $this->buatTugas($proker, 'Tugas Belum', selesai: false);

        $this->assertEquals(50, $proker->fresh()->progress_persen);

        // Ubah hanya nama_tugas (bukan is_selesai) → observer tidak memanggil updateProgress
        $tugasBelum->update(['nama_tugas' => 'Nama Baru']);

        // Progress tetap 50%
        $this->assertEquals(50, $proker->fresh()->progress_persen);
    }
}
