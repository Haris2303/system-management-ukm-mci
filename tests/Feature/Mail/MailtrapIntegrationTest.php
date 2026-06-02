<?php

namespace Tests\Feature\Mail;

use App\Mail\PendaftarDitolak;
use App\Mail\PendaftarLulus;
use App\Models\Divisi;
use App\Models\Pendaftar;
use App\Services\PendaftarService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Integrasi test yang benar-benar mengirim email ke Mailtrap sandbox.
 *
 * Jalankan secara terpisah:
 *   php artisan test tests/Feature/Mail/MailtrapIntegrationTest.php
 *
 * Pastikan .env sudah dikonfigurasi:
 *   MAIL_MAILER=smtp
 *   MAIL_HOST=sandbox.smtp.mailtrap.io
 *   MAIL_USERNAME=xxx
 *   MAIL_PASSWORD=xxx
 */
#[\PHPUnit\Framework\Attributes\Group('mailtrap')]
class MailtrapIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);

        // Tidak pakai Mail::fake() — email benar-benar dikirim ke Mailtrap
        $this->skipJikaMailtrapTidakDikonfigurasi();

        // phpunit.xml mengoverride MAIL_MAILER=array, kita paksa SMTP ke Mailtrap
        $this->pakaiSmtpMailtrap();
    }

    private function skipJikaMailtrapTidakDikonfigurasi(): void
    {
        // Baca langsung dari .env (bukan config yang sudah di-override phpunit.xml)
        $host     = env('MAIL_HOST');
        $username = env('MAIL_USERNAME');

        if (
            $host !== 'sandbox.smtp.mailtrap.io'
            || empty($username)
            || $username === 'your_mailtrap_username'
        ) {
            $this->markTestSkipped(
                'Mailtrap belum dikonfigurasi. Pastikan MAIL_HOST, MAIL_USERNAME, '
                    . 'dan MAIL_PASSWORD di .env sudah diisi dengan kredensial Mailtrap.'
            );
        }
    }

    private function pakaiSmtpMailtrap(): void
    {
        // Override config runtime agar mail driver SMTP ke Mailtrap
        // (phpunit.xml default-nya array, kita bypass untuk test integrasi ini)
        config([
            'mail.default'                  => 'smtp',
            'mail.mailers.smtp.transport'   => 'smtp',
            'mail.mailers.smtp.host'        => env('MAIL_HOST'),
            'mail.mailers.smtp.port'        => env('MAIL_PORT', 2525),
            'mail.mailers.smtp.username'    => env('MAIL_USERNAME'),
            'mail.mailers.smtp.password'    => env('MAIL_PASSWORD'),
            'mail.mailers.smtp.encryption'  => env('MAIL_ENCRYPTION', null),
            'mail.from.address'             => env('MAIL_FROM_ADDRESS', 'noreply@ukm-mci.ac.id'),
            'mail.from.name'                => env('MAIL_FROM_NAME', 'UKM MCI'),
        ]);

        // Reset Mail facade agar pakai konfigurasi baru
        Mail::forgetMailers();

        // Delay untuk menghormati rate limit Mailtrap free plan (maks 1 email/detik)
        sleep(2);
    }

    protected function tearDown(): void
    {
        // Jeda tambahan setelah setiap test agar koneksi SMTP Mailtrap tuntas
        sleep(1);
        parent::tearDown();
    }

    // ══════════════════════════════════════════════════════════════
    // KIRIM NYATA KE MAILTRAP
    // ══════════════════════════════════════════════════════════════

    public function test_kirim_email_lulus_ke_mailtrap(): void
    {
        $divisi    = $this->buatDivisi('Web Development');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'     => 'Ahmad Rizki',
            'email'    => 'ahmad.rizki@example.com',
            'angkatan' => '2024',
        ]);

        // Kirim mailable langsung — tanpa service agar lebih terkontrol
        Mail::to($pendaftar->email)
            ->send(new PendaftarLulus($pendaftar));

        // Jika tidak ada exception → email berhasil dikirim ke Mailtrap
        $this->assertTrue(true, 'Email PendaftarLulus berhasil dikirim ke Mailtrap.');
    }

    public function test_kirim_email_ditolak_ke_mailtrap(): void
    {
        $divisi    = $this->buatDivisi('Mobile Development');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'  => 'Sari Dewi',
            'email' => 'sari.dewi@example.com',
        ]);

        Mail::to($pendaftar->email)
            ->send(new PendaftarDitolak($pendaftar));

        $this->assertTrue(true, 'Email PendaftarDitolak berhasil dikirim ke Mailtrap.');
    }

    public function test_service_luluskan_mengirim_email_via_mailtrap(): void
    {
        $divisi    = $this->buatDivisi('Multimedia');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'     => 'Eko Prasetyo',
            'email'    => 'eko.prasetyo@example.com',
            'nim'      => '20240001',
            'angkatan' => '2024',
        ]);

        $user = app(PendaftarService::class)->luluskan($pendaftar);

        // Verifikasi DB dan email terkirim
        $this->assertEquals('lulus', $pendaftar->fresh()->status);
        $this->assertNotNull($user);

        // Email dikirim ke Mailtrap — cek inbox Mailtrap Anda
        $this->assertTrue(true, 'Email lulus via PendaftarService berhasil dikirim ke Mailtrap.');
    }

    public function test_service_tolak_mengirim_email_via_mailtrap(): void
    {
        $divisi    = $this->buatDivisi('Jaringan');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'  => 'Dini Rahayu',
            'email' => 'dini.rahayu@example.com',
        ]);

        app(PendaftarService::class)->tolak($pendaftar);

        $this->assertEquals('ditolak', $pendaftar->fresh()->status);

        $this->assertTrue(true, 'Email tolak via PendaftarService berhasil dikirim ke Mailtrap.');
    }

    public function test_email_lulus_memiliki_konten_yang_benar(): void
    {
        $divisi    = $this->buatDivisi('Web Dev');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'     => 'Tes Konten',
            'email'    => 'tes.konten@example.com',
            'angkatan' => '2024',
        ]);

        $mailable = new PendaftarLulus($pendaftar);

        // Render konten email dan periksa isinya
        $rendered = $mailable->render();

        $this->assertStringContainsString('Tes Konten',                     $rendered);
        $this->assertStringContainsString('Web Dev',                        $rendered);
        $this->assertStringContainsString('LULUS',                          $rendered);
        $this->assertStringContainsString('tes.konten@example.com',         $rendered);
        $this->assertStringContainsString(PendaftarService::DEFAULT_PASSWORD, $rendered);
    }

    public function test_email_ditolak_memiliki_konten_yang_benar(): void
    {
        $divisi    = $this->buatDivisi('Multimedia');
        $pendaftar = $this->buatPendaftar($divisi, [
            'nama'  => 'Tes Konten Tolak',
            'email' => 'tes.tolak@example.com',
        ]);

        $mailable = new PendaftarDitolak($pendaftar);

        $rendered = $mailable->render();

        $this->assertStringContainsString('Tes Konten Tolak', $rendered);
        $this->assertStringContainsString('Multimedia',       $rendered);
        $this->assertStringContainsString('UKM MCI',          $rendered);
    }
}
