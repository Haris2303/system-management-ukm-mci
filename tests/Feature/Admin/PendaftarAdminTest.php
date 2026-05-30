<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Pendaftars\Pages\ListPendaftars;
use App\Filament\Resources\Pendaftars\Pages\ViewPendaftar;
use App\Filament\Resources\Pendaftars\PendaftarResource;
use App\Models\Divisi;
use App\Models\JawabanPendaftar;
use App\Models\Pendaftar;
use App\Models\PertanyaanSeleksi;
use App\Models\User;
use App\Services\PendaftarService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PendaftarAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ─── helpers ───────────────────────────────────────────────

    private function buatDivisi(string $nama = 'Web Dev'): Divisi
    {
        static $urut = 1;
        return Divisi::create([
            'nama'      => $nama,
            'slug'      => Str::slug($nama . '-' . $urut++),
            'icon'      => '💻',
            'is_active' => true,
            'urut'      => $urut,
        ]);
    }

    private function buatUser(string $role, array $attrs = []): User
    {
        /** @var User $user */
        $user = User::factory()->create($attrs);
        $user->assignRole($role);
        return $user;
    }

    private function buatPendaftar(Divisi $divisi, array $attrs = []): Pendaftar
    {
        return Pendaftar::create(array_merge([
            'divisi_id' => $divisi->id,
            'nama'      => 'Budi Santoso',
            'nim'       => '2023' . rand(1000, 9999),
            'email'     => 'pendaftar@example.com',
            'no_hp'     => '081234567890',
            'angkatan'  => '2023',
            'status'    => 'menunggu',
        ], $attrs));
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — canViewAny & getEloquentQuery
    // ══════════════════════════════════════════════════════════════

    public function test_ketua_ukm_dapat_akses_resource_pendaftar(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(PendaftarResource::canViewAny());
    }

    public function test_super_admin_dapat_akses_resource_pendaftar(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(PendaftarResource::canViewAny());
    }

    public function test_ketua_divisi_dapat_akses_resource_pendaftar(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertTrue(PendaftarResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_akses_resource_pendaftar(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(PendaftarResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_akses_resource_pendaftar(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(PendaftarResource::canViewAny());
    }

    // ── Query scoping ────────────────────────────────────────────

    public function test_ketua_ukm_melihat_semua_pendaftar(): void
    {
        $divisiA = $this->buatDivisi('A');
        $divisiB = $this->buatDivisi('B');
        $admin   = $this->buatUser('ketua_ukm');

        $this->buatPendaftar($divisiA);
        $this->buatPendaftar($divisiB);

        $this->actingAs($admin);

        $query = PendaftarResource::getEloquentQuery();
        $this->assertEquals(2, $query->count());
    }

    public function test_ketua_divisi_hanya_melihat_pendaftar_divisinya(): void
    {
        $divisiA     = $this->buatDivisi('A');
        $divisiB     = $this->buatDivisi('B');
        $ketuaDivisi = $this->buatUser('ketua_divisi', ['divisi_id' => $divisiA->id]);

        $this->buatPendaftar($divisiA, ['nama' => 'Pendaftar A']);
        $this->buatPendaftar($divisiA, ['nama' => 'Pendaftar A2', 'nim' => '20230001']);
        $this->buatPendaftar($divisiB, ['nama' => 'Pendaftar B']);

        $this->actingAs($ketuaDivisi);

        $query  = PendaftarResource::getEloquentQuery();
        $result = $query->pluck('nama');

        $this->assertEquals(2, $result->count());
        $this->assertContains('Pendaftar A',  $result);
        $this->assertNotContains('Pendaftar B', $result);
    }

    public function test_ketua_divisi_tanpa_divisi_tidak_melihat_pendaftar_apapun(): void
    {
        $divisi      = $this->buatDivisi();
        $ketuaDivisi = $this->buatUser('ketua_divisi', ['divisi_id' => null]);

        $this->buatPendaftar($divisi);

        $this->actingAs($ketuaDivisi);

        $query = PendaftarResource::getEloquentQuery();
        $this->assertEquals(0, $query->count());
    }

    // ══════════════════════════════════════════════════════════════
    // LULUSKAN — PendaftarService::luluskan
    // ══════════════════════════════════════════════════════════════

    public function test_luluskan_mengubah_status_ke_lulus(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        app(PendaftarService::class)->luluskan($pendaftar);

        $this->assertEquals('lulus', $pendaftar->fresh()->status);
    }

    public function test_luluskan_membuat_akun_user_baru(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'budi.baru@example.com']);

        $this->assertDatabaseMissing('users', ['email' => 'budi.baru@example.com']);

        app(PendaftarService::class)->luluskan($pendaftar);

        $this->assertDatabaseHas('users', ['email' => 'budi.baru@example.com']);
    }

    public function test_luluskan_akun_menggunakan_email_dari_formulir_pendaftaran(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, [
            'email' => 'budi.formulir@example.com',
            'nim'   => '20230001',
        ]);

        app(PendaftarService::class)->luluskan($pendaftar);

        // Akun dibuat dengan email formulir — bukan dummy NIM@mci.ac.id
        $this->assertDatabaseHas('users', ['email' => 'budi.formulir@example.com']);
        $this->assertDatabaseMissing('users', ['email' => '20230001@mci.ac.id']);
    }

    public function test_luluskan_gagal_jika_pendaftar_tidak_memiliki_email(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => null, 'nim' => '20230002']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('tidak memiliki email');

        app(PendaftarService::class)->luluskan($pendaftar);
    }

    public function test_luluskan_memberi_role_anggota_pada_user_baru(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['nim' => '20230003']);

        $user = app(PendaftarService::class)->luluskan($pendaftar);

        $this->assertTrue($user->hasRole('anggota'));
    }

    public function test_luluskan_menyalin_divisi_ke_akun_user(): void
    {
        $divisi    = $this->buatDivisi('Web Dev');
        $pendaftar = $this->buatPendaftar($divisi, ['nim' => '20230004']);

        $user = app(PendaftarService::class)->luluskan($pendaftar);

        $this->assertEquals($divisi->id, $user->divisi_id);
    }

    public function test_luluskan_menyimpan_user_id_ke_pendaftar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['nim' => '20230005']);

        $user = app(PendaftarService::class)->luluskan($pendaftar);

        $this->assertEquals($user->id, $pendaftar->fresh()->user_id);
    }

    public function test_luluskan_gagal_jika_email_dipakai_user_lain(): void
    {
        $divisi     = $this->buatDivisi();
        User::factory()->create(['email' => 'pakai@example.com']);
        $pendaftar  = $this->buatPendaftar($divisi, ['email' => 'pakai@example.com']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sudah digunakan akun lain');

        app(PendaftarService::class)->luluskan($pendaftar);
    }

    public function test_luluskan_gagal_jika_email_formulir_sudah_dipakai_akun_lain(): void
    {
        $divisi    = $this->buatDivisi();
        User::factory()->create(['email' => 'pakai.lain@example.com']); // user tak terkait
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'pakai.lain@example.com']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sudah digunakan akun lain');

        app(PendaftarService::class)->luluskan($pendaftar);
    }

    public function test_luluskan_tidak_duplikat_role_jika_dipanggil_ulang(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['nim' => '20230007']);

        // Luluskan pertama kali
        $user = app(PendaftarService::class)->luluskan($pendaftar);
        $this->assertTrue($user->hasRole('anggota'));
        $this->assertCount(1, $user->roles);

        // Asosiasikan kembali dan luluskan lagi (simulasi re-luluskan dengan user sudah terhubung)
        // Karena user_id sudah diset, service tidak akan throw collision
        $pendaftar->update(['status' => 'menunggu']); // reset status untuk test
        $user2 = app(PendaftarService::class)->luluskan($pendaftar);

        // Role tidak duplikat
        $this->assertCount(1, $user2->roles);
        $this->assertTrue($user2->hasRole('anggota'));
    }

    // ══════════════════════════════════════════════════════════════
    // TOLAK — action tolak
    // ══════════════════════════════════════════════════════════════

    public function test_tolak_mengubah_status_ke_ditolak(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        app(PendaftarService::class)->tolak($pendaftar);

        $this->assertEquals('ditolak', $pendaftar->fresh()->status);
    }

    public function test_tolak_tidak_membuat_akun_user(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['nim' => '20230099']);

        app(PendaftarService::class)->tolak($pendaftar);

        $this->assertDatabaseMissing('users', ['email' => '20230099@mci.ac.id']);
    }

    // ══════════════════════════════════════════════════════════════
    // NAVIGATION BADGE — jumlah pendaftar menunggu
    // ══════════════════════════════════════════════════════════════

    public function test_navigation_badge_menampilkan_jumlah_pendaftar_menunggu(): void
    {
        $divisi = $this->buatDivisi();
        $this->buatPendaftar($divisi, ['status' => 'menunggu']);
        $this->buatPendaftar($divisi, ['status' => 'menunggu', 'nim' => '2023A']);
        $this->buatPendaftar($divisi, ['status' => 'lulus',    'nim' => '2023B']);
        $this->buatPendaftar($divisi, ['status' => 'ditolak',  'nim' => '2023C']);

        $badge = PendaftarResource::getNavigationBadge();
        $this->assertEquals('2', $badge);
    }

    public function test_navigation_badge_nol_jika_tidak_ada_yang_menunggu(): void
    {
        $divisi = $this->buatDivisi();
        $this->buatPendaftar($divisi, ['status' => 'lulus', 'nim' => '2023A']);

        $badge = PendaftarResource::getNavigationBadge();
        $this->assertEquals('0', $badge);
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL — helpers & scopes
    // ══════════════════════════════════════════════════════════════

    public function test_is_pending_sesuai_status(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'menunggu']);

        $this->assertTrue($pendaftar->isPending());
        $this->assertFalse($pendaftar->isApproved());
        $this->assertFalse($pendaftar->isRejected());
    }

    public function test_is_approved_sesuai_status(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'lulus', 'nim' => '2023A']);

        $this->assertTrue($pendaftar->isApproved());
        $this->assertFalse($pendaftar->isPending());
        $this->assertFalse($pendaftar->isRejected());
    }

    public function test_is_rejected_sesuai_status(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'ditolak', 'nim' => '2023A']);

        $this->assertTrue($pendaftar->isRejected());
        $this->assertFalse($pendaftar->isPending());
        $this->assertFalse($pendaftar->isApproved());
    }

    public function test_effective_email_menggunakan_email_asli_jika_ada(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, [
            'email' => 'budi@example.com',
            'nim'   => '20230001',
        ]);

        $this->assertEquals('budi@example.com', $pendaftar->effectiveEmail());
    }

    public function test_effective_email_fallback_ke_dummy_jika_tidak_ada_email(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => null, 'nim' => '20230002']);

        $this->assertEquals('20230002@mci.ac.id', $pendaftar->effectiveEmail());
    }

    public function test_generate_dummy_email_format_nim_at_mci(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['nim' => 'MCI2023001']);

        $this->assertEquals('mci2023001@mci.ac.id', $pendaftar->generateDummyEmail());
    }

    public function test_generate_dummy_email_lowercase(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['nim' => 'ABC123']);

        $this->assertEquals('abc123@mci.ac.id', $pendaftar->generateDummyEmail());
    }

    public function test_total_skor_menjumlahkan_nilai_jawaban(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        $pertanyaan1 = PertanyaanSeleksi::create([
            'divisi_id'       => $divisi->id,
            'pertanyaan_teks' => 'Pertanyaan 1',
            'is_active'       => true,
            'urut'            => 1,
        ]);
        $pertanyaan2 = PertanyaanSeleksi::create([
            'divisi_id'       => $divisi->id,
            'pertanyaan_teks' => 'Pertanyaan 2',
            'is_active'       => true,
            'urut'            => 2,
        ]);

        JawabanPendaftar::create([
            'pendaftar_id'  => $pendaftar->id,
            'pertanyaan_id' => $pertanyaan1->id,
            'jawaban_teks'  => 'Jawaban 1',
            'nilai_skor'    => 80,
        ]);
        JawabanPendaftar::create([
            'pendaftar_id'  => $pendaftar->id,
            'pertanyaan_id' => $pertanyaan2->id,
            'jawaban_teks'  => 'Jawaban 2',
            'nilai_skor'    => 90,
        ]);

        $this->assertEquals(170, $pendaftar->totalSkor());
    }

    public function test_total_skor_mengabaikan_jawaban_yang_belum_dinilai(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        $pertanyaan = PertanyaanSeleksi::create([
            'divisi_id'       => $divisi->id,
            'pertanyaan_teks' => 'Pertanyaan',
            'is_active'       => true,
            'urut'            => 1,
        ]);

        JawabanPendaftar::create([
            'pendaftar_id'  => $pendaftar->id,
            'pertanyaan_id' => $pertanyaan->id,
            'jawaban_teks'  => 'Jawaban',
            'nilai_skor'    => null, // belum dinilai
        ]);

        $this->assertEquals(0, $pendaftar->totalSkor());
    }

    public function test_rata_skor_menghitung_rata_rata_dengan_benar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        $p1 = PertanyaanSeleksi::create([
            'divisi_id' => $divisi->id, 'pertanyaan_teks' => 'P1', 'is_active' => true, 'urut' => 1,
        ]);
        $p2 = PertanyaanSeleksi::create([
            'divisi_id' => $divisi->id, 'pertanyaan_teks' => 'P2', 'is_active' => true, 'urut' => 2,
        ]);

        JawabanPendaftar::create([
            'pendaftar_id' => $pendaftar->id, 'pertanyaan_id' => $p1->id,
            'jawaban_teks' => 'J1', 'nilai_skor' => 80,
        ]);
        JawabanPendaftar::create([
            'pendaftar_id' => $pendaftar->id, 'pertanyaan_id' => $p2->id,
            'jawaban_teks' => 'J2', 'nilai_skor' => 90,
        ]);

        $this->assertEquals(85.0, $pendaftar->rataSkor());
    }

    public function test_rata_skor_nol_jika_belum_ada_penilaian(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        $this->assertEquals(0.0, $pendaftar->rataSkor());
    }

    // ── Unique constraint ────────────────────────────────────────

    public function test_unique_constraint_satu_nim_per_divisi(): void
    {
        $divisi = $this->buatDivisi();
        $this->buatPendaftar($divisi, ['nim' => '20230001']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->buatPendaftar($divisi, ['nim' => '20230001']); // duplikat
    }

    public function test_nim_sama_boleh_daftar_di_divisi_berbeda(): void
    {
        $divisiA = $this->buatDivisi('A');
        $divisiB = $this->buatDivisi('B');

        $this->buatPendaftar($divisiA, ['nim' => '20230001']);
        $this->buatPendaftar($divisiB, ['nim' => '20230001']); // tidak konflik

        $this->assertDatabaseCount('pendaftars', 2);
    }

    // ══════════════════════════════════════════════════════════════
    // LIVEWIRE — halaman list & aksi
    // ══════════════════════════════════════════════════════════════

    public function test_list_page_dapat_diakses_oleh_ketua_ukm(): void
    {
        $admin = $this->buatUser('ketua_ukm');

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->assertSuccessful();
    }

    public function test_list_page_menampilkan_semua_pendaftar_untuk_ketua_ukm(): void
    {
        $divisiA = $this->buatDivisi('A');
        $divisiB = $this->buatDivisi('B');
        $admin   = $this->buatUser('ketua_ukm');

        $p1 = $this->buatPendaftar($divisiA, ['nama' => 'Budi']);
        $p2 = $this->buatPendaftar($divisiB, ['nama' => 'Sari']);

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->assertCanSeeTableRecords([$p1, $p2]);
    }

    public function test_list_page_ketua_divisi_hanya_lihat_divisinya(): void
    {
        $divisiA     = $this->buatDivisi('A');
        $divisiB     = $this->buatDivisi('B');
        $ketuaDivisi = $this->buatUser('ketua_divisi', ['divisi_id' => $divisiA->id]);

        $p1 = $this->buatPendaftar($divisiA, ['nama' => 'Anggota A']);
        $p2 = $this->buatPendaftar($divisiB, ['nama' => 'Anggota B']);

        Livewire::actingAs($ketuaDivisi)
                ->test(ListPendaftars::class)
                ->assertCanSeeTableRecords([$p1])
                ->assertCanNotSeeTableRecords([$p2]);
    }

    public function test_aksi_luluskan_tersedia_untuk_pendaftar_menunggu(): void
    {
        $divisi    = $this->buatDivisi();
        $admin     = $this->buatUser('ketua_ukm');
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'menunggu']);

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->assertTableActionExists('luluskan')
                ->assertTableActionVisible('luluskan', $pendaftar);
    }

    public function test_aksi_tolak_tersedia_untuk_pendaftar_menunggu(): void
    {
        $divisi    = $this->buatDivisi();
        $admin     = $this->buatUser('ketua_ukm');
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'menunggu']);

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->assertTableActionExists('tolak')
                ->assertTableActionVisible('tolak', $pendaftar);
    }

    public function test_aksi_luluskan_tidak_tersedia_untuk_yang_sudah_lulus(): void
    {
        $divisi    = $this->buatDivisi();
        $admin     = $this->buatUser('ketua_ukm');
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'lulus', 'nim' => '2023A']);

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->assertTableActionHidden('luluskan', $pendaftar);
    }

    public function test_aksi_tolak_tidak_tersedia_untuk_yang_sudah_ditolak(): void
    {
        $divisi    = $this->buatDivisi();
        $admin     = $this->buatUser('ketua_ukm');
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'ditolak', 'nim' => '2023A']);

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->assertTableActionHidden('tolak', $pendaftar);
    }

    public function test_eksekusi_aksi_luluskan_dari_tabel(): void
    {
        $divisi    = $this->buatDivisi();
        $admin     = $this->buatUser('ketua_ukm');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nim'    => '20239999',
            'email'  => 'peserta.lulus@example.com',
            'status' => 'menunggu',
        ]);

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->callTableAction('luluskan', $pendaftar)
                ->assertHasNoTableActionErrors();

        $this->assertEquals('lulus', $pendaftar->fresh()->status);
        // Akun dibuat dengan email yang diinput di formulir
        $this->assertDatabaseHas('users', ['email' => 'peserta.lulus@example.com']);
    }

    public function test_eksekusi_aksi_tolak_dari_tabel(): void
    {
        $divisi    = $this->buatDivisi();
        $admin     = $this->buatUser('ketua_ukm');
        $pendaftar = $this->buatPendaftar($divisi, ['status' => 'menunggu']);

        Livewire::actingAs($admin)
                ->test(ListPendaftars::class)
                ->callTableAction('tolak', $pendaftar)
                ->assertHasNoTableActionErrors();

        $this->assertEquals('ditolak', $pendaftar->fresh()->status);
    }

    public function test_view_page_menampilkan_detail_pendaftar(): void
    {
        $divisi    = $this->buatDivisi();
        $admin     = $this->buatUser('ketua_ukm');
        $pendaftar = $this->buatPendaftar($divisi, ['nama' => 'Detail Test']);

        Livewire::actingAs($admin)
                ->test(ViewPendaftar::class, ['record' => $pendaftar->getKey()])
                ->assertSuccessful()
                ->assertSee('Detail Test');
    }
}
