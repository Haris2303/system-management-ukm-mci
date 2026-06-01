<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Divisis\DivisiResource;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Filament\Resources\Materis\MateriResource;
use App\Filament\Resources\Pendaftars\PendaftarResource;
use App\Filament\Resources\PertanyaanSeleksis\PertanyaanSeleksiResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\RagDocuments\RagDocumentResource;
use App\Filament\Resources\TagihanKas\TagihanKasResource;
use App\Filament\Resources\TransaksiKas\TransaksiKasResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Materi;
use App\Models\PertanyaanSeleksi;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Seed semua role & permission sesuai konfigurasi produksi
        $this->seed(RolePermissionSeeder::class);
    }

    // ══════════════════════════════════════════════════════════════
    // canAccessPanel — siapa yang boleh masuk panel admin
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_akses_panel(): void
    {
        $user  = $this->buatUser('super_admin');
        $panel = Filament::getPanel('administrasi');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_ketua_ukm_dapat_akses_panel(): void
    {
        $user  = $this->buatUser('ketua_ukm');
        $panel = Filament::getPanel('administrasi');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_sekretaris_dapat_akses_panel(): void
    {
        $user  = $this->buatUser('sekretaris');
        $panel = Filament::getPanel('administrasi');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_bendahara_dapat_akses_panel(): void
    {
        $user  = $this->buatUser('bendahara');
        $panel = Filament::getPanel('administrasi');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_ketua_divisi_dapat_akses_panel(): void
    {
        $user  = $this->buatUser('ketua_divisi');
        $panel = Filament::getPanel('administrasi');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_anggota_tidak_dapat_akses_panel(): void
    {
        $user  = $this->buatUser('anggota');
        $panel = Filament::getPanel('administrasi');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_demisioner_tidak_dapat_akses_panel(): void
    {
        $user  = $this->buatUser('demisioner');
        $panel = Filament::getPanel('administrasi');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_user_yang_di_kick_tidak_dapat_akses_panel(): void
    {
        $user = $this->buatUser('anggota', [
            'kicked_at' => now(),
        ]);
        $panel = Filament::getPanel('administrasi');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    // ══════════════════════════════════════════════════════════════
    // HTTP — redirect untuk yang tidak berhak
    // ══════════════════════════════════════════════════════════════

    public function test_tamu_diarahkan_ke_halaman_login_panel(): void
    {
        $this->get('/administrasi')
            ->assertRedirect('/administrasi/login');
    }

    public function test_anggota_tidak_dapat_akses_panel_via_http(): void
    {
        $user = $this->buatUser('anggota');

        // Filament menolak akses: 403 atau redirect ke login
        $response = $this->actingAs($user)->get('/administrasi');
        $this->assertTrue(
            $response->status() === 403 || $response->isRedirect(),
            "Anggota seharusnya tidak bisa akses panel (dapat 403 atau redirect)."
        );
    }

    public function test_demisioner_tidak_dapat_akses_panel_via_http(): void
    {
        $user = $this->buatUser('demisioner');

        $response = $this->actingAs($user)->get('/administrasi');
        $this->assertTrue(
            $response->status() === 403 || $response->isRedirect(),
            "Demisioner seharusnya tidak bisa akses panel (dapat 403 atau redirect)."
        );
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: UserResource — kelola_users (hanya super_admin)
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_kelola_users(): void
    {
        $this->aktingAs($this->buatUser('super_admin'));
        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(UserResource::canCreate());
    }

    public function test_ketua_ukm_tidak_dapat_kelola_users(): void
    {
        $this->aktingAs($this->buatUser('ketua_ukm'));
        $this->assertFalse(UserResource::canViewAny());
        $this->assertFalse(UserResource::canCreate());
    }

    public function test_sekretaris_tidak_dapat_kelola_users(): void
    {
        $this->aktingAs($this->buatUser('sekretaris'));
        $this->assertFalse(UserResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_kelola_users(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertFalse(UserResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_kelola_users(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(UserResource::canViewAny());
    }

    public function test_super_admin_tidak_dapat_hapus_dirinya_sendiri(): void
    {
        $user = $this->buatUser('super_admin');
        $this->aktingAs($user);

        // Self-deletion protection
        $this->assertFalse(UserResource::canDelete($user));
    }

    public function test_super_admin_dapat_hapus_user_lain(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');
        $this->aktingAs($admin);

        $this->assertTrue(UserResource::canDelete($target));
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: DivisiResource — kelola_divisi
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_kelola_divisi(): void
    {
        $this->aktingAs($this->buatUser('super_admin'));
        $this->assertTrue(DivisiResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_kelola_divisi(): void
    {
        $this->aktingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(DivisiResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_kelola_divisi(): void
    {
        $this->aktingAs($this->buatUser('sekretaris'));
        $this->assertFalse(DivisiResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_kelola_divisi(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertFalse(DivisiResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_kelola_divisi(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(DivisiResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: TagihanKas & TransaksiKas — kelola_tagihan_kas
    // ══════════════════════════════════════════════════════════════

    public function test_bendahara_dapat_kelola_tagihan_kas(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertTrue(TagihanKasResource::canViewAny());
        $this->assertTrue(TransaksiKasResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_kelola_tagihan_kas(): void
    {
        $this->aktingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(TagihanKasResource::canViewAny());
        $this->assertTrue(TransaksiKasResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_kelola_tagihan_kas(): void
    {
        $this->aktingAs($this->buatUser('sekretaris'));
        $this->assertFalse(TagihanKasResource::canViewAny());
        $this->assertFalse(TransaksiKasResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_kelola_tagihan_kas(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(TagihanKasResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: MateriResource — kelola_materi
    // ══════════════════════════════════════════════════════════════

    public function test_sekretaris_dapat_kelola_materi(): void
    {
        $this->aktingAs($this->buatUser('sekretaris'));
        $this->assertTrue(MateriResource::canViewAny());
    }

    public function test_ketua_divisi_dapat_kelola_materi(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertTrue(MateriResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_kelola_materi(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertFalse(MateriResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: PendaftarResource — kelola_pendaftar / kelola_pendaftar_divisi
    // ══════════════════════════════════════════════════════════════

    public function test_ketua_ukm_dapat_kelola_pendaftar(): void
    {
        $this->aktingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(PendaftarResource::canViewAny());
    }

    public function test_ketua_divisi_dapat_kelola_pendaftar_divisinya(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertTrue(PendaftarResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_kelola_pendaftar(): void
    {
        $this->aktingAs($this->buatUser('sekretaris'));
        $this->assertFalse(PendaftarResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_kelola_pendaftar(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertFalse(PendaftarResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: PertanyaanSeleksiResource — kelola_pertanyaan_seleksi
    // ══════════════════════════════════════════════════════════════

    public function test_ketua_ukm_dapat_kelola_pertanyaan_seleksi(): void
    {
        $this->aktingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(PertanyaanSeleksiResource::canViewAny());
    }

    public function test_ketua_divisi_dapat_kelola_pertanyaan_seleksi(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertTrue(PertanyaanSeleksiResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_kelola_pertanyaan_seleksi(): void
    {
        $this->aktingAs($this->buatUser('sekretaris'));
        $this->assertFalse(PertanyaanSeleksiResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_kelola_pertanyaan_seleksi(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertFalse(PertanyaanSeleksiResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: PostResource — kelola_berita
    // ══════════════════════════════════════════════════════════════

    public function test_sekretaris_dapat_kelola_berita(): void
    {
        $this->aktingAs($this->buatUser('sekretaris'));
        $this->assertTrue(PostResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_kelola_berita(): void
    {
        $this->aktingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(PostResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_kelola_berita(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertFalse(PostResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_kelola_berita(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(PostResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // Resource: RagDocumentResource — kelola_rag_documents
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_kelola_rag_documents(): void
    {
        $this->aktingAs($this->buatUser('super_admin'));
        $this->assertTrue(RagDocumentResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_kelola_rag_documents(): void
    {
        $this->aktingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(RagDocumentResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_kelola_rag_documents(): void
    {
        $this->aktingAs($this->buatUser('bendahara'));
        $this->assertFalse(RagDocumentResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_kelola_rag_documents(): void
    {
        $this->aktingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(RagDocumentResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // PERTANYAAN SELEKSI RESOURCE — kelola_pertanyaan_seleksi + scoping
    // ══════════════════════════════════════════════════════════════

    public function test_ketua_ukm_dapat_akses_pertanyaan_seleksi(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(PertanyaanSeleksiResource::canViewAny());
    }

    public function test_ketua_divisi_dapat_akses_pertanyaan_seleksi(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertTrue(PertanyaanSeleksiResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_akses_pertanyaan_seleksi(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(PertanyaanSeleksiResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_akses_pertanyaan_seleksi(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(PertanyaanSeleksiResource::canViewAny());
    }

    public function test_ketua_divisi_hanya_melihat_pertanyaan_divisinya(): void
    {
        $divisiA     = $this->buatDivisi('A');
        $divisiB     = $this->buatDivisi('B');
        $ketuaDivisi = $this->buatUser('ketua_divisi', ['divisi_id' => $divisiA->id]);

        PertanyaanSeleksi::create(['divisi_id' => $divisiA->id, 'pertanyaan_teks' => 'PA1', 'is_active' => true, 'urut' => 1]);
        PertanyaanSeleksi::create(['divisi_id' => $divisiA->id, 'pertanyaan_teks' => 'PA2', 'is_active' => true, 'urut' => 2]);
        PertanyaanSeleksi::create(['divisi_id' => $divisiB->id, 'pertanyaan_teks' => 'PB1', 'is_active' => true, 'urut' => 1]);

        $this->actingAs($ketuaDivisi);
        $result = PertanyaanSeleksiResource::getEloquentQuery()->pluck('pertanyaan_teks');

        $this->assertCount(2, $result);
        $this->assertContains('PA1', $result);
        $this->assertContains('PA2', $result);
        $this->assertNotContains('PB1', $result);
    }

    public function test_ketua_ukm_melihat_semua_pertanyaan(): void
    {
        $divisiA   = $this->buatDivisi('A');
        $divisiB   = $this->buatDivisi('B');
        $ketuaUkm  = $this->buatUser('ketua_ukm');

        PertanyaanSeleksi::create(['divisi_id' => $divisiA->id, 'pertanyaan_teks' => 'PA', 'is_active' => true, 'urut' => 1]);
        PertanyaanSeleksi::create(['divisi_id' => $divisiB->id, 'pertanyaan_teks' => 'PB', 'is_active' => true, 'urut' => 1]);

        $this->actingAs($ketuaUkm);
        $this->assertEquals(2, PertanyaanSeleksiResource::getEloquentQuery()->count());
    }

    // ══════════════════════════════════════════════════════════════
    // MATERI RESOURCE (ADMIN) — kelola_materi + query scoping
    // ══════════════════════════════════════════════════════════════

    public function test_sekretaris_dapat_akses_materi_resource(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertTrue(MateriResource::canViewAny());
    }

    public function test_ketua_divisi_dapat_akses_materi_resource(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertTrue(MateriResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_akses_materi_resource(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(MateriResource::canViewAny());
    }

    public function test_sekretaris_melihat_semua_materi(): void
    {
        $divisi    = $this->buatDivisi();
        $sekretaris = $this->buatUser('sekretaris');

        Materi::create(['judul' => 'Umum',  'divisi_id' => null,       'uploaded_by' => $sekretaris->id]);
        Materi::create(['judul' => 'Divisi', 'divisi_id' => $divisi->id, 'uploaded_by' => $sekretaris->id]);

        $this->actingAs($sekretaris);
        $this->assertEquals(2, MateriResource::getEloquentQuery()->count());
    }

    public function test_ketua_divisi_hanya_melihat_materi_divisinya_dan_umum(): void
    {
        $divisiA     = $this->buatDivisi('A');
        $divisiB     = $this->buatDivisi('B');
        $ketuaDivisi = $this->buatUser('ketua_divisi', ['divisi_id' => $divisiA->id]);
        $author      = User::factory()->create();

        Materi::create(['judul' => 'Umum',     'divisi_id' => null,        'uploaded_by' => $author->id]);
        Materi::create(['judul' => 'Divisi A', 'divisi_id' => $divisiA->id, 'uploaded_by' => $author->id]);
        Materi::create(['judul' => 'Divisi B', 'divisi_id' => $divisiB->id, 'uploaded_by' => $author->id]);

        $this->actingAs($ketuaDivisi);
        $result = MateriResource::getEloquentQuery()->pluck('judul');

        $this->assertCount(2, $result);
        $this->assertContains('Umum',     $result);
        $this->assertContains('Divisi A', $result);
        $this->assertNotContains('Divisi B', $result);
    }

    // ══════════════════════════════════════════════════════════════
    // TRANSAKSI KAS RESOURCE — kelola_tagihan_kas
    // ══════════════════════════════════════════════════════════════

    public function test_bendahara_dapat_akses_transaksi_kas(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertTrue(TransaksiKasResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_akses_transaksi_kas(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(TransaksiKasResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_akses_transaksi_kas(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(TransaksiKasResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_akses_transaksi_kas(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(TransaksiKasResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // Rangkuman: semua role yang boleh masuk panel
    // ══════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\DataProvider('roleBolehAksesPanelProvider')]
    public function test_role_dapat_akses_panel(string $role): void
    {
        $user  = $this->buatUser($role);
        $panel = Filament::getPanel('administrasi');

        $this->assertTrue(
            $user->canAccessPanel($panel),
            "Role '{$role}' seharusnya bisa akses panel admin."
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('roleTidakBolehAksesPanelProvider')]
    public function test_role_tidak_dapat_akses_panel(string $role): void
    {
        $user  = $this->buatUser($role);
        $panel = Filament::getPanel('administrasi');

        $this->assertFalse(
            $user->canAccessPanel($panel),
            "Role '{$role}' seharusnya TIDAK bisa akses panel admin."
        );
    }

    public static function roleBolehAksesPanelProvider(): array
    {
        return [
            'super_admin'  => ['super_admin'],
            'ketua_ukm'    => ['ketua_ukm'],
            'sekretaris'   => ['sekretaris'],
            'bendahara'    => ['bendahara'],
            'ketua_divisi' => ['ketua_divisi'],
        ];
    }

    public static function roleTidakBolehAksesPanelProvider(): array
    {
        return [
            'anggota'    => ['anggota'],
            'demisioner' => ['demisioner'],
        ];
    }
}
