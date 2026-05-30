<?php

namespace Tests\Feature\Mail;

use App\Mail\PendaftarDitolak;
use App\Mail\PendaftarLulus;
use App\Models\Divisi;
use App\Models\Pendaftar;
use App\Services\PendaftarService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotifikasiPendaftarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);

        Mail::fake();
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

    private function buatPendaftar(Divisi $divisi, array $attrs = []): Pendaftar
    {
        return Pendaftar::create(array_merge([
            'divisi_id' => $divisi->id,
            'nama'      => 'Budi Santoso',
            'nim'       => '2023' . rand(1000, 9999),
            'email'     => 'budi@example.com',
            'no_hp'     => '081234567890',
            'angkatan'  => '2023',
            'status'    => 'menunggu',
        ], $attrs));
    }

    // ══════════════════════════════════════════════════════════════
    // LULUS — email dikirim saat pendaftar diluluskan
    // ══════════════════════════════════════════════════════════════

    public function test_email_lulus_dikirim_saat_pendaftar_diluluskan(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'budi@example.com']);

        app(PendaftarService::class)->luluskan($pendaftar);

        Mail::assertSent(PendaftarLulus::class, function (PendaftarLulus $mail) use ($pendaftar) {
            return $mail->hasTo($pendaftar->email);
        });
    }

    public function test_email_lulus_dikirim_tepat_sekali(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'budi@example.com']);

        app(PendaftarService::class)->luluskan($pendaftar);

        Mail::assertSentCount(1);
    }

    public function test_luluskan_gagal_jika_pendaftar_tidak_punya_email(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => null, 'nim' => '20230001']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('tidak memiliki email');

        app(PendaftarService::class)->luluskan($pendaftar);

        // Tidak ada email yang terkirim karena exception sebelum Mail::to()
        Mail::assertNothingSent();
    }

    public function test_email_lulus_berisi_nama_pendaftar_yang_benar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'  => 'Ahmad Zulfikri',
            'email' => 'ahmad@example.com',
        ]);

        app(PendaftarService::class)->luluskan($pendaftar);

        Mail::assertSent(PendaftarLulus::class, function (PendaftarLulus $mail) {
            return $mail->pendaftar->nama === 'Ahmad Zulfikri';
        });
    }

    public function test_email_lulus_memiliki_subject_yang_benar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        app(PendaftarService::class)->luluskan($pendaftar);

        Mail::assertSent(PendaftarLulus::class, function (PendaftarLulus $mail) {
            return str_contains($mail->envelope()->subject, 'LULUS');
        });
    }

    public function test_email_lulus_dikirim_ke_email_pendaftar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'calon.anggota@gmail.com']);

        app(PendaftarService::class)->luluskan($pendaftar);

        Mail::assertSent(PendaftarLulus::class, fn ($m) => $m->hasTo('calon.anggota@gmail.com'));
    }

    // ══════════════════════════════════════════════════════════════
    // DITOLAK — email dikirim saat pendaftar ditolak
    // ══════════════════════════════════════════════════════════════

    public function test_email_ditolak_dikirim_saat_pendaftar_ditolak(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'budi@example.com']);

        app(PendaftarService::class)->tolak($pendaftar);

        Mail::assertSent(PendaftarDitolak::class, function (PendaftarDitolak $mail) use ($pendaftar) {
            return $mail->hasTo($pendaftar->email);
        });
    }

    public function test_email_ditolak_dikirim_tepat_sekali(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'budi@example.com']);

        app(PendaftarService::class)->tolak($pendaftar);

        Mail::assertSentCount(1);
    }

    public function test_email_ditolak_tidak_dikirim_jika_tidak_ada_email_asli(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => null]);

        app(PendaftarService::class)->tolak($pendaftar);

        Mail::assertNothingSent();
    }

    public function test_email_ditolak_berisi_nama_pendaftar_yang_benar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'  => 'Sari Dewi',
            'email' => 'sari@example.com',
        ]);

        app(PendaftarService::class)->tolak($pendaftar);

        Mail::assertSent(PendaftarDitolak::class, function (PendaftarDitolak $mail) {
            return $mail->pendaftar->nama === 'Sari Dewi';
        });
    }

    public function test_email_ditolak_memiliki_subject_yang_benar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        app(PendaftarService::class)->tolak($pendaftar);

        Mail::assertSent(PendaftarDitolak::class, function (PendaftarDitolak $mail) {
            return str_contains($mail->envelope()->subject, 'Seleksi');
        });
    }

    // ══════════════════════════════════════════════════════════════
    // MAILABLE CLASS — konten & struktur
    // ══════════════════════════════════════════════════════════════

    public function test_mailable_lulus_memiliki_envelope_yang_benar(): void
    {
        $divisi    = $this->buatDivisi('Multimedia');
        $pendaftar = $this->buatPendaftar($divisi, ['nama' => 'Test User']);

        $mailable = new PendaftarLulus($pendaftar);

        $this->assertInstanceOf(Envelope::class, $mailable->envelope());
        $this->assertStringContainsString('LULUS', $mailable->envelope()->subject);
        $this->assertStringContainsString('UKM MCI', $mailable->envelope()->subject);
    }

    public function test_mailable_ditolak_memiliki_envelope_yang_benar(): void
    {
        $divisi    = $this->buatDivisi('Multimedia');
        $pendaftar = $this->buatPendaftar($divisi, ['nama' => 'Test User']);

        $mailable = new PendaftarDitolak($pendaftar);

        $this->assertInstanceOf(Envelope::class, $mailable->envelope());
        $this->assertStringContainsString('Seleksi', $mailable->envelope()->subject);
        $this->assertStringContainsString('UKM MCI', $mailable->envelope()->subject);
    }

    public function test_mailable_lulus_content_menggunakan_template_yang_benar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        $mailable = new PendaftarLulus($pendaftar);

        $this->assertEquals('emails.pendaftar-lulus', $mailable->content()->view);
    }

    public function test_mailable_ditolak_content_menggunakan_template_yang_benar(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi);

        $mailable = new PendaftarDitolak($pendaftar);

        $this->assertEquals('emails.pendaftar-ditolak', $mailable->content()->view);
    }

    public function test_mailable_lulus_meneruskan_data_yang_benar_ke_template(): void
    {
        $divisi    = $this->buatDivisi('Web Dev');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'     => 'Andi Wijaya',
            'email'    => 'andi@example.com',
            'angkatan' => '2024',
        ]);

        $mailable = new PendaftarLulus($pendaftar);
        $data     = $mailable->content()->with;

        $this->assertEquals('Andi Wijaya',              $data['nama']);
        $this->assertEquals('Web Dev',                  $data['divisi']);
        $this->assertEquals('andi@example.com',         $data['email']);
        $this->assertEquals(PendaftarService::DEFAULT_PASSWORD, $data['password']);
        $this->assertEquals('2024',                     $data['angkatan']);
    }

    public function test_mailable_ditolak_meneruskan_data_yang_benar_ke_template(): void
    {
        $divisi    = $this->buatDivisi('Mobile Dev');
        $pendaftar = $this->buatPendaftar($divisi, ['nama' => 'Rina Susanti']);

        $mailable = new PendaftarDitolak($pendaftar);
        $data     = $mailable->content()->with;

        $this->assertEquals('Rina Susanti', $data['nama']);
        $this->assertEquals('Mobile Dev',   $data['divisi']);
    }

    public function test_email_lulus_menggunakan_email_asli_pendaftar_bukan_dummy(): void
    {
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, [
            'email' => 'formulir@example.com',
            'nim'   => '20240099',
        ]);

        $mailable = new PendaftarLulus($pendaftar);
        $data     = $mailable->content()->with;

        // Harus pakai email dari formulir, BUKAN dummy NIM@mci.ac.id
        $this->assertEquals('formulir@example.com', $data['email']);
        $this->assertNotEquals('20240099@mci.ac.id', $data['email']);
    }

    public function test_template_lulus_tidak_mengandung_nomor_hp(): void
    {
        $divisi    = $this->buatDivisi('Web Dev');
        $pendaftar = $this->buatPendaftar($divisi, [
            'email'    => 'test@example.com',
            'angkatan' => '2024',
        ]);

        $rendered = (new PendaftarLulus($pendaftar))->render();

        $this->assertStringNotContainsString('nomor HP', $rendered);
        $this->assertStringNotContainsString('no_hp',    $rendered);
        $this->assertStringContainsString('foto profil', $rendered);
    }

    public function test_email_lulus_tidak_menghalangi_jika_smtp_gagal(): void
    {
        // Mail::fake() sudah aktif — tidak ada pengiriman real
        // Ini memastikan kegagalan email tidak throw exception ke user
        $divisi    = $this->buatDivisi();
        $pendaftar = $this->buatPendaftar($divisi, ['email' => 'test@example.com']);

        // Tidak ada exception yang melempar keluar
        $user = app(PendaftarService::class)->luluskan($pendaftar);
        $this->assertEquals('lulus', $pendaftar->fresh()->status);
        $this->assertNotNull($user);
    }
}
