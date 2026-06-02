<?php

namespace Tests\Feature\Api;

use App\Models\Agenda;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PresensiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'anggota',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'demisioner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    // ══════════════════════════════════════════════════════════════
    // CATAT PRESENSI — POST /api/presensi
    // ══════════════════════════════════════════════════════════════

    public function test_presensi_berhasil_dicatat_dengan_qr_code_valid(): void
    {
        $agenda = $this->buatAgenda();
        $user   = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertCreated()
            ->assertJsonPath('status', 'berhasil')
            ->assertJsonPath('data.status', 'Hadir')
            ->assertJsonPath('data.agenda', $agenda->nama_agenda);
    }

    public function test_presensi_tersimpan_di_database(): void
    {
        $agenda = $this->buatAgenda();
        $user   = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertCreated();

        $this->assertDatabaseHas('presensis', [
            'user_id'   => $user->id,
            'agenda_id' => $agenda->id,
            'status'    => 'Hadir',
        ]);
    }

    public function test_presensi_menyertakan_nama_user_dalam_pesan_selamat_datang(): void
    {
        $agenda = $this->buatAgenda();
        $user   = $this->aktifUser(['name' => 'Budi Santoso']);

        $response = $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertCreated();

        $this->assertStringContainsString('Budi Santoso', $response->json('pesan'));
    }

    // ── QR Code tidak valid ─────────────────────────────────────

    public function test_presensi_gagal_jika_qr_code_tidak_cocok(): void
    {
        $this->buatAgenda();
        $user = $this->aktifUser();

        // Token 32 karakter valid tapi tidak cocok dengan agenda manapun
        $tokenPalsu = str_repeat('x', 32);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $tokenPalsu])
            ->assertNotFound()
            ->assertJsonPath('status', 'gagal');
    }

    public function test_presensi_gagal_jika_agenda_tidak_aktif(): void
    {
        // Agenda sengaja dinonaktifkan (bukan karena expired)
        $agenda = $this->buatAgenda(['is_active' => false]);
        $user   = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertNotFound()
            ->assertJsonPath('status', 'gagal');
    }

    // ── Validasi Waktu ──────────────────────────────────────────

    public function test_presensi_gagal_jika_waktu_sudah_selesai(): void
    {
        // Waktu selesai sudah lewat (agenda akan di-auto-close oleh retrieved hook,
        // tapi controller tetap mengecek waktu dan mengembalikan 400)
        $agenda = $this->buatAgenda([
            'waktu_mulai'   => now()->subHours(3),
            'waktu_selesai' => now()->subHour(),
        ]);
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertStatus(400)
            ->assertJsonPath('status', 'gagal')
            ->assertJsonFragment(['pesan' => 'Jadwal presensi sudah ditutup.']);
    }

    public function test_presensi_gagal_jika_belum_waktunya_mulai(): void
    {
        $agenda = $this->buatAgenda([
            'waktu_mulai'   => now()->addHour(),
            'waktu_selesai' => now()->addHours(3),
        ]);
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertStatus(400)
            ->assertJsonPath('status', 'Gagal')
            ->assertJsonFragment(['pesan' => 'Jadwal presensi belum dimulai.']);
    }

    // ── Duplikasi ───────────────────────────────────────────────

    public function test_presensi_gagal_jika_user_sudah_presensi_sebelumnya(): void
    {
        $agenda = $this->buatAgenda();
        $user   = $this->aktifUser();

        // Presensi pertama
        Presensi::create([
            'user_id'   => $user->id,
            'agenda_id' => $agenda->id,
            'jam_hadir' => now(),
            'status'    => 'Hadir',
        ]);

        // Coba presensi lagi → harus ditolak
        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertUnprocessable()
            ->assertJsonFragment(['pesan' => 'Anda sudah melakukan presensi untuk agenda ini.']);
    }

    public function test_user_berbeda_dapat_presensi_di_agenda_yang_sama(): void
    {
        $agenda = $this->buatAgenda();
        $user1  = $this->aktifUser();
        $user2  = $this->aktifUser();

        $this->withToken($this->tokenFor($user1))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertCreated();

        // Reset auth guard agar request berikutnya tidak memakai cache user sebelumnya
        $this->app['auth']->forgetGuards();

        $this->withToken($this->tokenFor($user2))
            ->postJson('/api/presensi', ['token' => $agenda->qr_code_token])
            ->assertCreated();

        $this->assertDatabaseCount('presensis', 2);
    }

    // ── Validasi Input ──────────────────────────────────────────

    public function test_presensi_gagal_jika_token_tidak_diisi(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['token']);
    }

    public function test_presensi_gagal_jika_token_bukan_32_karakter(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => 'terlalu-pendek'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['token']);
    }

    public function test_presensi_gagal_jika_token_lebih_dari_32_karakter(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/presensi', ['token' => str_repeat('a', 33)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['token']);
    }

    // ── Hak Akses: Catat Presensi ───────────────────────────────

    public function test_presensi_memerlukan_autentikasi(): void
    {
        $this->postJson('/api/presensi', ['token' => str_repeat('a', 32)])
            ->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // RIWAYAT PRESENSI — GET /api/presensi/riwayat
    // ══════════════════════════════════════════════════════════════

    public function test_riwayat_mengembalikan_presensi_milik_user_yang_login(): void
    {
        $agenda = $this->buatAgenda();
        $user   = $this->aktifUser();

        Presensi::create([
            'user_id'   => $user->id,
            'agenda_id' => $agenda->id,
            'jam_hadir' => now(),
            'status'    => 'Hadir',
        ]);

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/presensi/riwayat')
            ->assertOk()
            ->assertJsonPath('pesan', 'Riwayat presensi berhasil dimuat.')
            ->assertJsonCount(1, 'data.data');
    }

    public function test_riwayat_tidak_mengembalikan_presensi_user_lain(): void
    {
        $agenda       = $this->buatAgenda();
        $userLogin    = $this->aktifUser();
        $userLain     = $this->aktifUser();

        // Buat presensi untuk user lain
        Presensi::create([
            'user_id'   => $userLain->id,
            'agenda_id' => $agenda->id,
            'jam_hadir' => now(),
            'status'    => 'Hadir',
        ]);

        $this->withToken($this->tokenFor($userLogin))
            ->getJson('/api/presensi/riwayat')
            ->assertOk()
            ->assertJsonCount(0, 'data.data');
    }

    public function test_riwayat_diurutkan_terbaru_dahulu(): void
    {
        $agenda1 = $this->buatAgenda(['nama_agenda' => 'Agenda Lama']);
        $agenda2 = $this->buatAgenda(['nama_agenda' => 'Agenda Baru']);
        $user    = $this->aktifUser();

        Presensi::create([
            'user_id'   => $user->id,
            'agenda_id' => $agenda1->id,
            'jam_hadir' => now()->subDays(2),
            'status'    => 'Hadir',
        ]);
        Presensi::create([
            'user_id'   => $user->id,
            'agenda_id' => $agenda2->id,
            'jam_hadir' => now()->subDay(),
            'status'    => 'Hadir',
        ]);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/presensi/riwayat')
            ->assertOk();

        $items = $response->json('data.data');
        $this->assertEquals($agenda2->id, $items[0]['agenda_id']);
        $this->assertEquals($agenda1->id, $items[1]['agenda_id']);
    }

    public function test_riwayat_memiliki_struktur_pagination(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/presensi/riwayat')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'data',
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);
    }

    public function test_riwayat_menyertakan_nama_agenda(): void
    {
        $agenda = $this->buatAgenda(['nama_agenda' => 'Rapat Bulanan']);
        $user   = $this->aktifUser();

        Presensi::create([
            'user_id'   => $user->id,
            'agenda_id' => $agenda->id,
            'jam_hadir' => now(),
            'status'    => 'Hadir',
        ]);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/presensi/riwayat')
            ->assertOk();

        $item = $response->json('data.data.0');
        $this->assertEquals('Rapat Bulanan', $item['agenda']['nama_agenda']);
    }

    public function test_riwayat_kosong_jika_belum_pernah_presensi(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/presensi/riwayat')
            ->assertOk()
            ->assertJsonCount(0, 'data.data')
            ->assertJsonPath('data.total', 0);
    }

    // ── Hak Akses: Riwayat ─────────────────────────────────────

    public function test_riwayat_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/presensi/riwayat')->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // PERILAKU AGENDA — auto-close & pencatatan absen
    // ══════════════════════════════════════════════════════════════

    public function test_agenda_otomatis_ditutup_saat_diambil_setelah_waktu_selesai(): void
    {
        // Buat agenda langsung di DB agar booted() creating tidak override is_active
        $agenda = Agenda::create([
            'nama_agenda'   => 'Agenda Kadaluarsa',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHours(3),
            'waktu_selesai' => now()->subMinutes(30),
            'lokasi'        => 'Ruang B',
            'is_active'     => true,
        ]);

        // Paksa re-fetch dari DB — ini memicu retrieved hook → close()
        $agendaFresh = Agenda::find($agenda->id);

        $this->assertFalse($agendaFresh->is_active);
    }

    public function test_anggota_yang_tidak_hadir_dicatat_absen_saat_agenda_ditutup(): void
    {
        $anggota1 = $this->aktifUser(['name' => 'Anggota Hadir']);
        $anggota2 = $this->aktifUser(['name' => 'Anggota Absen']);

        $agenda = Agenda::create([
            'nama_agenda'   => 'Agenda Selesai',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHours(2),
            'waktu_selesai' => now()->subMinutes(10),
            'lokasi'        => 'Ruang C',
            'is_active'     => true,
        ]);

        // Anggota1 sudah hadir sebelum agenda ditutup
        Presensi::create([
            'user_id'   => $anggota1->id,
            'agenda_id' => $agenda->id,
            'jam_hadir' => now()->subHour(),
            'status'    => 'Hadir',
        ]);

        // Trigger auto-close via retrieved hook
        Agenda::find($agenda->id);

        // Anggota1 tetap Hadir, anggota2 dicatat Absen
        $this->assertDatabaseHas('presensis', [
            'user_id'   => $anggota1->id,
            'agenda_id' => $agenda->id,
            'status'    => 'Hadir',
        ]);
        $this->assertDatabaseHas('presensis', [
            'user_id'   => $anggota2->id,
            'agenda_id' => $agenda->id,
            'status'    => 'Absen',
        ]);
    }

    public function test_super_admin_tidak_dicatat_absen_saat_agenda_ditutup(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $agenda = Agenda::create([
            'nama_agenda'   => 'Agenda Selesai',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHours(2),
            'waktu_selesai' => now()->subMinutes(10),
            'lokasi'        => 'Online',
            'is_active'     => true,
        ]);

        Agenda::find($agenda->id); // trigger close

        $this->assertDatabaseMissing('presensis', [
            'user_id'   => $admin->id,
            'agenda_id' => $agenda->id,
        ]);
    }

    public function test_demisioner_tidak_dicatat_absen_saat_agenda_ditutup(): void
    {
        $demisioner = User::factory()->create();
        $demisioner->assignRole('demisioner');

        $agenda = Agenda::create([
            'nama_agenda'   => 'Agenda Selesai',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHours(2),
            'waktu_selesai' => now()->subMinutes(10),
            'lokasi'        => 'Online',
            'is_active'     => true,
        ]);

        Agenda::find($agenda->id); // trigger close

        $this->assertDatabaseMissing('presensis', [
            'user_id'   => $demisioner->id,
            'agenda_id' => $agenda->id,
        ]);
    }

    public function test_close_agenda_tidak_membuat_duplikat_jika_dipanggil_dua_kali(): void
    {
        $user = $this->aktifUser();

        $agenda = Agenda::create([
            'nama_agenda'   => 'Agenda Selesai',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHours(2),
            'waktu_selesai' => now()->subMinutes(10),
            'lokasi'        => 'Online',
            'is_active'     => true,
        ]);

        // Panggil close() dua kali — panggilan kedua harus di-ignore karena is_active sudah false
        $agenda->close();
        $agenda->close();

        // Hanya 1 record Absen yang dibuat untuk $user, tidak ada duplikat
        $this->assertDatabaseCount('presensis', 1);
        $this->assertDatabaseHas('presensis', [
            'user_id'   => $user->id,
            'agenda_id' => $agenda->id,
            'status'    => 'Absen',
        ]);
    }
}
