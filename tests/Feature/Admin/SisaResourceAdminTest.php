<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\RekapPresensi;
use App\Filament\Resources\Divisis\DivisiResource;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Filament\Resources\Materis\MateriResource;
use App\Filament\Resources\PertanyaanSeleksis\PertanyaanSeleksiResource;
use App\Filament\Resources\TransaksiKas\TransaksiKasResource;
use App\Models\Agenda;
use App\Models\Divisi;
use App\Models\Gallery;
use App\Models\Materi;
use App\Models\Pendaftar;
use App\Models\Presensi;
use App\Models\PertanyaanSeleksi;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class SisaResourceAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ─── helpers ───────────────────────────────────────────────

    private function buatUser(string $role, array $attrs = []): User
    {
        /** @var User $user */
        $user = User::factory()->create($attrs);
        $user->assignRole($role);
        return $user;
    }

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

    // ══════════════════════════════════════════════════════════════
    // DIVISI RESOURCE — kelola_divisi (super_admin & ketua_ukm)
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_akses_divisi(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(DivisiResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_akses_divisi(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(DivisiResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_akses_divisi(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(DivisiResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_akses_divisi(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(DivisiResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_akses_resource_divisi(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(DivisiResource::canViewAny());
    }

    // ── Model Divisi ─────────────────────────────────────────────

    public function test_divisi_auto_generate_slug_dari_nama(): void
    {
        $divisi = Divisi::create([
            'nama'      => 'Web Development',
            'icon'      => '💻',
            'is_active' => true,
            'urut'      => 1,
        ]);

        $this->assertEquals('web-development', $divisi->slug);
    }

    public function test_scope_active_hanya_mengembalikan_divisi_aktif(): void
    {
        Divisi::create(['nama' => 'Aktif',    'slug' => 'aktif',    'icon' => '✅', 'is_active' => true,  'urut' => 1]);
        Divisi::create(['nama' => 'Nonaktif', 'slug' => 'nonaktif', 'icon' => '❌', 'is_active' => false, 'urut' => 2]);

        $result = Divisi::active()->get();

        $this->assertCount(1, $result);
        $this->assertEquals('Aktif', $result->first()->nama);
    }

    public function test_scope_active_mengurutkan_berdasarkan_urut(): void
    {
        Divisi::create(['nama' => 'B', 'slug' => 'b', 'icon' => '💻', 'is_active' => true, 'urut' => 2]);
        Divisi::create(['nama' => 'A', 'slug' => 'a', 'icon' => '💻', 'is_active' => true, 'urut' => 1]);

        $result = Divisi::active()->pluck('nama');

        $this->assertEquals('A', $result->first());
        $this->assertEquals('B', $result->last());
    }

    // ══════════════════════════════════════════════════════════════
    // GALLERY RESOURCE — tidak ada canViewAny → semua role panel
    // ══════════════════════════════════════════════════════════════

    public function test_semua_role_panel_dapat_akses_galeri(): void
    {
        foreach (['super_admin', 'ketua_ukm', 'sekretaris', 'bendahara', 'ketua_divisi'] as $role) {
            $admin = $this->buatUser($role);
            Livewire::actingAs($admin)
                ->test(ListGalleries::class)
                ->assertSuccessful();
        }
    }

    // ── Model Gallery ────────────────────────────────────────────

    public function test_gallery_is_featured_default_false(): void
    {
        $gallery = Gallery::create([
            'judul'       => 'Foto Kegiatan',
            'foto'        => 'gallery/foto.jpg',
            'kategori'    => 'Kegiatan',
            'is_featured' => false,
        ]);

        $this->assertFalse($gallery->is_featured);
    }

    public function test_gallery_is_featured_true_tampil_di_landing(): void
    {
        Gallery::create(['judul' => 'Foto Unggulan', 'foto' => 'g/a.jpg', 'kategori' => 'Umum', 'is_featured' => true]);
        Gallery::create(['judul' => 'Foto Biasa',    'foto' => 'g/b.jpg', 'kategori' => 'Umum', 'is_featured' => false]);

        $featured = Gallery::where('is_featured', true)->get();
        $this->assertCount(1, $featured);
        $this->assertEquals('Foto Unggulan', $featured->first()->judul);
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
    // REKAP PRESENSI PAGE — tidak ada canAccess override
    // ══════════════════════════════════════════════════════════════

    public function test_semua_role_panel_dapat_akses_rekap_presensi(): void
    {
        foreach (['super_admin', 'ketua_ukm', 'sekretaris', 'bendahara', 'ketua_divisi'] as $role) {
            $admin = $this->buatUser($role);
            Livewire::actingAs($admin)
                ->test(RekapPresensi::class)
                ->assertSuccessful();
        }
    }

    public function test_rekap_presensi_menampilkan_anggota_tanpa_super_admin_dan_demisioner(): void
    {
        $admin      = $this->buatUser('super_admin');
        $anggota1   = $this->buatUser('anggota');
        $anggota2   = $this->buatUser('anggota');
        $demisioner = $this->buatUser('demisioner');

        Livewire::actingAs($admin)
            ->test(RekapPresensi::class)
            ->assertCountTableRecords(2) // anggota1 dan anggota2, bukan super_admin/demisioner
            ->assertCanSeeTableRecords([$anggota1, $anggota2])
            ->assertCanNotSeeTableRecords([$admin, $demisioner]);
    }

    public function test_rekap_presensi_menghitung_jumlah_hadir_izin_absen(): void
    {
        $admin   = $this->buatUser('super_admin');
        $anggota = $this->buatUser('anggota');

        $agenda1 = Agenda::create([
            'nama_agenda'   => 'Rapat 1',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHours(2),
            'waktu_selesai' => now()->subHour(),
            'lokasi'        => 'Online',
            'is_active'     => false,
            'qr_code_token' => Str::random(32),
        ]);
        $agenda2 = Agenda::create([
            'nama_agenda'   => 'Rapat 2',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHours(2),
            'waktu_selesai' => now()->subHour(),
            'lokasi'        => 'Online',
            'is_active'     => false,
            'qr_code_token' => Str::random(32),
        ]);

        Presensi::create(['user_id' => $anggota->id, 'agenda_id' => $agenda1->id, 'status' => 'Hadir', 'jam_hadir' => now()]);
        Presensi::create(['user_id' => $anggota->id, 'agenda_id' => $agenda2->id, 'status' => 'Absen', 'jam_hadir' => now()]);

        $response = Livewire::actingAs($admin)
            ->test(RekapPresensi::class)
            ->assertSuccessful();

        // Verifikasi data presensi tersimpan benar
        $this->assertEquals(1, Presensi::where('user_id', $anggota->id)->where('status', 'Hadir')->count());
        $this->assertEquals(1, Presensi::where('user_id', $anggota->id)->where('status', 'Absen')->count());
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL PENDAFTAR — uniqueSlug & scope
    // ══════════════════════════════════════════════════════════════

    public function test_pendaftar_scope_pending_hanya_mengembalikan_yang_menunggu(): void
    {
        $divisi = $this->buatDivisi();

        Pendaftar::create(['divisi_id' => $divisi->id, 'nama' => 'A', 'nim' => '001', 'status' => 'menunggu', 'angkatan' => '2023']);
        Pendaftar::create(['divisi_id' => $divisi->id, 'nama' => 'B', 'nim' => '002', 'status' => 'lulus',    'angkatan' => '2023']);
        Pendaftar::create(['divisi_id' => $divisi->id, 'nama' => 'C', 'nim' => '003', 'status' => 'ditolak',  'angkatan' => '2023']);

        $this->assertCount(1, Pendaftar::pending()->get());
        $this->assertCount(1, Pendaftar::approved()->get());
        $this->assertCount(1, Pendaftar::rejected()->get());
    }
}
