<?php

namespace Tests\Feature\Api;

use App\Models\Divisi;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MateriTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'anggota',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'demisioner', 'guard_name' => 'web']);

        Storage::fake('public');
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

    /** Buat materi umum (divisi_id = null). */
    private function buatMateriUmum(array $attrs = []): Materi
    {
        return Materi::create(array_merge([
            'judul'      => 'Materi Umum',
            'deskripsi'  => 'Deskripsi materi umum',
            'divisi_id'  => null,
        ], $attrs));
    }

    /** Buat materi khusus divisi tertentu. */
    private function buatMateriDivisi(Divisi $divisi, array $attrs = []): Materi
    {
        return Materi::create(array_merge([
            'judul'     => 'Materi Divisi ' . $divisi->nama,
            'deskripsi' => 'Deskripsi materi divisi',
            'divisi_id' => $divisi->id,
        ], $attrs));
    }

    // ══════════════════════════════════════════════════════════════
    // DAFTAR MATERI — GET /api/materi
    // ══════════════════════════════════════════════════════════════

    // ── Filter & akses ──────────────────────────────────────────

    public function test_index_mengembalikan_materi_umum_untuk_semua_user(): void
    {
        $this->buatMateriUmum(['judul' => 'Materi A']);
        $this->buatMateriUmum(['judul' => 'Materi B']);

        $user = $this->userTanpaDivisi();

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertCount(2, $response->json('data.materi'));
    }

    public function test_index_mengembalikan_materi_umum_dan_divisi_user(): void
    {
        $divisi = $this->buatDivisi('Web Dev');
        $user   = $this->userDenganDivisi($divisi);

        $this->buatMateriUmum();
        $this->buatMateriDivisi($divisi);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertCount(2, $response->json('data.materi'));
    }

    public function test_index_tidak_mengembalikan_materi_divisi_lain(): void
    {
        $divisiA = $this->buatDivisi('Web Dev');
        $divisiB = $this->buatDivisi('Mobile Dev');
        $userA   = $this->userDenganDivisi($divisiA);

        $this->buatMateriDivisi($divisiA, ['judul' => 'Materi A']);
        $this->buatMateriDivisi($divisiB, ['judul' => 'Materi B']);

        $response = $this->withToken($this->tokenFor($userA))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertCount(1, $response->json('data.materi'));
        $this->assertEquals('Materi A', $response->json('data.materi.0.judul'));
    }

    public function test_index_user_tanpa_divisi_hanya_melihat_materi_umum(): void
    {
        $divisi = $this->buatDivisi();
        $user   = $this->userTanpaDivisi();

        $this->buatMateriUmum(['judul' => 'Umum']);
        $this->buatMateriDivisi($divisi, ['judul' => 'Khusus']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertCount(1, $response->json('data.materi'));
        $this->assertEquals('Umum', $response->json('data.materi.0.judul'));
    }

    // ── Statistik ───────────────────────────────────────────────

    public function test_index_statistik_total_jumlah_umum_dan_divisi(): void
    {
        $divisi = $this->buatDivisi();
        $user   = $this->userDenganDivisi($divisi);

        $this->buatMateriUmum();
        $this->buatMateriUmum();
        $this->buatMateriDivisi($divisi);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertEquals(3, $response->json('data.total'));
        $this->assertEquals(2, $response->json('data.jumlah_umum'));
        $this->assertEquals(1, $response->json('data.jumlah_divisi'));
    }

    // ── Field response ──────────────────────────────────────────

    public function test_index_field_data_lengkap(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum();

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $materi = $response->json('data.materi.0');
        $this->assertArrayHasKey('id',         $materi);
        $this->assertArrayHasKey('judul',       $materi);
        $this->assertArrayHasKey('deskripsi',   $materi);
        $this->assertArrayHasKey('has_file',    $materi);
        $this->assertArrayHasKey('file_url',    $materi);
        $this->assertArrayHasKey('file_size',   $materi);
        $this->assertArrayHasKey('has_link',    $materi);
        $this->assertArrayHasKey('link_url',    $materi);
        $this->assertArrayHasKey('is_umum',     $materi);
        $this->assertArrayHasKey('jenis',       $materi);
        $this->assertArrayHasKey('jenis_label', $materi);
        $this->assertArrayHasKey('uploader',    $materi);
        $this->assertArrayHasKey('tanggal',     $materi);
    }

    public function test_index_flag_is_umum_benar(): void
    {
        $divisi = $this->buatDivisi();
        $user   = $this->userDenganDivisi($divisi);

        $this->buatMateriUmum(['judul' => 'Umum']);
        $this->buatMateriDivisi($divisi, ['judul' => 'Divisi']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $materis    = collect($response->json('data.materi'));
        $umum       = $materis->firstWhere('judul', 'Umum');
        $divisiItem = $materis->firstWhere('judul', 'Divisi');

        $this->assertTrue($umum['is_umum']);
        $this->assertFalse($divisiItem['is_umum']);
    }

    public function test_index_jenis_label_umum_dan_divisi(): void
    {
        $divisi = $this->buatDivisi('Web Dev');
        $user   = $this->userDenganDivisi($divisi);

        $this->buatMateriUmum(['judul' => 'Umum']);
        $this->buatMateriDivisi($divisi, ['judul' => 'Divisi']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $materis    = collect($response->json('data.materi'));
        $umum       = $materis->firstWhere('judul', 'Umum');
        $divisiItem = $materis->firstWhere('judul', 'Divisi');

        $this->assertEquals('umum',   $umum['jenis']);
        $this->assertEquals('divisi', $divisiItem['jenis']);
        $this->assertStringContainsString('Umum', $umum['jenis_label']);
        $this->assertStringContainsString('Web Dev', $divisiItem['jenis_label']);
    }

    // ── File & Link flags ───────────────────────────────────────

    public function test_index_has_file_true_jika_ada_file_path(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum(['file_path' => 'materi/dokumen.pdf']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertTrue($response->json('data.materi.0.has_file'));
        $this->assertNotNull($response->json('data.materi.0.file_url'));
    }

    public function test_index_has_file_false_jika_tidak_ada_file(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum(['file_path' => null]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertFalse($response->json('data.materi.0.has_file'));
        $this->assertNull($response->json('data.materi.0.file_url'));
    }

    public function test_index_has_link_true_jika_ada_link_url(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum(['link_url' => 'https://youtu.be/abc123']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertTrue($response->json('data.materi.0.has_link'));
        $this->assertEquals('https://youtu.be/abc123', $response->json('data.materi.0.link_url'));
    }

    public function test_index_has_link_false_jika_tidak_ada_link(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum(['link_url' => null]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertFalse($response->json('data.materi.0.has_link'));
        $this->assertNull($response->json('data.materi.0.link_url'));
    }

    public function test_index_materi_bisa_punya_file_dan_link_sekaligus(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum([
            'file_path' => 'materi/dokumen.pdf',
            'link_url'  => 'https://youtu.be/abc123',
        ]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $materi = $response->json('data.materi.0');
        $this->assertTrue($materi['has_file']);
        $this->assertTrue($materi['has_link']);
    }

    // ── Uploader ────────────────────────────────────────────────

    public function test_index_uploader_menampilkan_nama_pengunggah(): void
    {
        $uploader = User::factory()->create(['name' => 'Ketua Divisi']);
        $user     = $this->userTanpaDivisi();

        $this->buatMateriUmum(['uploaded_by' => $uploader->id]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertEquals('Ketua Divisi', $response->json('data.materi.0.uploader'));
    }

    public function test_index_uploader_fallback_admin_jika_tidak_ada_uploader(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum(['uploaded_by' => null]);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertEquals('Admin', $response->json('data.materi.0.uploader'));
    }

    // ── Pesan & terbaru dahulu ──────────────────────────────────

    public function test_index_diurutkan_terbaru_dahulu(): void
    {
        $user = $this->userTanpaDivisi();

        $lama = $this->buatMateriUmum(['judul' => 'Lama']);
        $baru = $this->buatMateriUmum(['judul' => 'Baru']);

        // Simulasi materi lama dibuat lebih dulu
        $lama->created_at = now()->subDay();
        $lama->save();

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertEquals('Baru', $response->json('data.materi.0.judul'));
        $this->assertEquals('Lama', $response->json('data.materi.1.judul'));
    }

    public function test_index_pesan_kosong_jika_tidak_ada_materi(): void
    {
        $user = $this->userTanpaDivisi();

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertStringContainsString('Belum ada materi', $response->json('pesan'));
        $this->assertEquals(0, $response->json('data.total'));
    }

    public function test_index_pesan_memuat_jumlah_materi(): void
    {
        $user = $this->userTanpaDivisi();
        $this->buatMateriUmum();
        $this->buatMateriUmum(['judul' => 'Materi 2']);

        $response = $this->withToken($this->tokenFor($user))
                         ->getJson('/api/materi')
                         ->assertOk();

        $this->assertStringContainsString('2', $response->json('pesan'));
    }

    // ── Hak Akses ───────────────────────────────────────────────

    public function test_index_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/materi')->assertUnauthorized();
    }

    public function test_index_diblokir_untuk_demisioner(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('demisioner');

        $this->withToken($this->tokenFor($user))
             ->getJson('/api/materi')
             ->assertForbidden()
             ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL — helpers & accessors
    // ══════════════════════════════════════════════════════════════

    public function test_has_file_true_jika_ada_file_path(): void
    {
        $materi = Materi::make(['file_path' => 'materi/file.pdf']);
        $this->assertTrue($materi->hasFile());
    }

    public function test_has_file_false_jika_tidak_ada_file_path(): void
    {
        $materi = Materi::make(['file_path' => null]);
        $this->assertFalse($materi->hasFile());
    }

    public function test_has_link_true_jika_ada_link_url(): void
    {
        $materi = Materi::make(['link_url' => 'https://example.com']);
        $this->assertTrue($materi->hasLink());
    }

    public function test_has_link_false_jika_tidak_ada_link_url(): void
    {
        $materi = Materi::make(['link_url' => null]);
        $this->assertFalse($materi->hasLink());
    }

    public function test_is_umum_true_jika_divisi_id_null(): void
    {
        $materi = Materi::make(['divisi_id' => null]);
        $this->assertTrue($materi->isUmum());
    }

    public function test_is_umum_false_jika_ada_divisi_id(): void
    {
        $divisi = $this->buatDivisi();
        $materi = Materi::make(['divisi_id' => $divisi->id]);
        $this->assertFalse($materi->isUmum());
    }

    public function test_file_url_mengembalikan_url_storage_jika_ada_file(): void
    {
        Storage::disk('public')->put('materi/dokumen.pdf', 'konten pdf');

        $materi = Materi::make(['file_path' => 'materi/dokumen.pdf']);

        $this->assertNotNull($materi->file_url);
        $this->assertStringContainsString('materi/dokumen.pdf', $materi->file_url);
    }

    public function test_file_url_null_jika_tidak_ada_file(): void
    {
        $materi = Materi::make(['file_path' => null]);
        $this->assertNull($materi->file_url);
    }

    public function test_file_size_format_bytes(): void
    {
        Storage::disk('public')->put('materi/kecil.pdf', str_repeat('x', 500));

        $materi = $this->buatMateriUmum(['file_path' => 'materi/kecil.pdf']);

        $this->assertStringContainsString('B', $materi->file_size);
    }

    public function test_file_size_format_kilobytes(): void
    {
        Storage::disk('public')->put('materi/sedang.pdf', str_repeat('x', 2048));

        $materi = $this->buatMateriUmum(['file_path' => 'materi/sedang.pdf']);

        $this->assertStringContainsString('KB', $materi->file_size);
    }

    public function test_file_size_null_jika_file_tidak_ada_di_storage(): void
    {
        // file_path diisi tapi file tidak ada di storage
        $materi = $this->buatMateriUmum(['file_path' => 'materi/tidak_ada.pdf']);
        $this->assertNull($materi->file_size);
    }

    public function test_file_size_null_jika_tidak_ada_file_path(): void
    {
        $materi = Materi::make(['file_path' => null]);
        $this->assertNull($materi->file_size);
    }

    // ── Scope forUser ───────────────────────────────────────────

    public function test_scope_for_user_dengan_divisi_mengembalikan_umum_dan_divisi(): void
    {
        $divisiA = $this->buatDivisi('A');
        $divisiB = $this->buatDivisi('B');

        $this->buatMateriUmum(['judul' => 'Umum']);
        $this->buatMateriDivisi($divisiA, ['judul' => 'Divisi A']);
        $this->buatMateriDivisi($divisiB, ['judul' => 'Divisi B']);

        $result = Materi::forUser($divisiA->id)->pluck('judul');

        $this->assertCount(2, $result);
        $this->assertContains('Umum',     $result);
        $this->assertContains('Divisi A', $result);
        $this->assertNotContains('Divisi B', $result);
    }

    public function test_scope_for_user_null_hanya_mengembalikan_materi_umum(): void
    {
        $divisi = $this->buatDivisi();
        $this->buatMateriUmum(['judul' => 'Umum']);
        $this->buatMateriDivisi($divisi, ['judul' => 'Khusus']);

        $result = Materi::forUser(null)->pluck('judul');

        $this->assertCount(1, $result);
        $this->assertContains('Umum', $result);
    }
}
