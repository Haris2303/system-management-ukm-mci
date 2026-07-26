<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\RekapPresensi;
use App\Filament\Resources\Agendas\Pages\ListAgendas;
use App\Filament\Resources\Agendas\Pages\ViewAgenda;
use App\Models\Agenda;
use App\Models\Presensi;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AgendaPresensiAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — AgendaResource (canViewAny mengecualikan bendahara)
    // Semua role panel selain bendahara dapat melihat Agenda
    // ══════════════════════════════════════════════════════════════

    public function test_semua_role_panel_dapat_akses_list_agenda(): void
    {
        foreach (['super_admin', 'sekretaris', 'ketua_divisi'] as $role) {
            $admin = $this->buatUser($role);
            Livewire::actingAs($admin)
                ->test(ListAgendas::class)
                ->assertSuccessful();
        }
    }

    public function test_bendahara_tidak_dapat_akses_list_agenda(): void
    {
        $admin = $this->buatUser('bendahara');
        Livewire::actingAs($admin)
            ->test(ListAgendas::class)
            ->assertForbidden();
    }

    public function test_ketua_ukm_tidak_dapat_akses_list_agenda(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        Livewire::actingAs($admin)
            ->test(ListAgendas::class)
            ->assertForbidden();
    }

    // ══════════════════════════════════════════════════════════════
    // VIEW PAGE — aksi catat_izin & tutup_agenda
    // ══════════════════════════════════════════════════════════════

    public function test_view_page_menampilkan_detail_agenda(): void
    {
        $admin  = $this->buatUser('sekretaris');
        $agenda = $this->buatAgenda(['nama_agenda' => 'Rapat Bulanan']);

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->assertSuccessful()
            ->assertSee('Rapat Bulanan');
    }

    public function test_aksi_tutup_agenda_tersedia_saat_aktif(): void
    {
        $admin  = $this->buatUser('sekretaris');
        $agenda = $this->buatAgenda(['is_active' => true]);

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->assertActionVisible('tutup_agenda');
    }

    public function test_aksi_tutup_agenda_tersembunyi_saat_tidak_aktif(): void
    {
        $admin  = $this->buatUser('sekretaris');
        $agenda = $this->buatAgenda(['is_active' => false]);

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->assertActionHidden('tutup_agenda');
    }

    public function test_eksekusi_tutup_agenda_menutup_dan_mencatat_absen(): void
    {
        $admin   = $this->buatUser('sekretaris');
        $anggota = $this->buatUser('anggota');
        // waktu_selesai di masa depan agar retrieved hook tidak auto-close
        $agenda  = $this->buatAgenda([
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addHours(2),
            'is_active'     => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->callAction('tutup_agenda')
            ->assertHasNoFormErrors();

        // Agenda ditutup
        $this->assertFalse($agenda->fresh()->is_active);

        // Anggota yang tidak hadir dicatat Absen
        $this->assertDatabaseHas('presensis', [
            'user_id'   => $anggota->id,
            'agenda_id' => $agenda->id,
            'status'    => 'Absen',
        ]);
    }

    public function test_aksi_catat_izin_tersedia_saat_agenda_aktif(): void
    {
        $admin  = $this->buatUser('sekretaris');
        $agenda = $this->buatAgenda(['is_active' => true]);

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->assertActionVisible('catat_izin');
    }

    public function test_aksi_catat_izin_tersembunyi_saat_agenda_tidak_aktif(): void
    {
        $admin  = $this->buatUser('sekretaris');
        $agenda = $this->buatAgenda(['is_active' => false]);

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->assertActionHidden('catat_izin');
    }

    public function test_eksekusi_catat_izin_mencatat_presensi_izin(): void
    {
        $admin   = $this->buatUser('sekretaris');
        $anggota = $this->buatUser('anggota');
        $agenda  = $this->buatAgenda();

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->callAction('catat_izin', data: ['user_ids' => [$anggota->id]])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('presensis', [
            'user_id'   => $anggota->id,
            'agenda_id' => $agenda->id,
            'status'    => 'Izin',
        ]);
    }

    public function test_catat_izin_tidak_duplikat_jika_dipanggil_dua_kali(): void
    {
        $admin   = $this->buatUser('sekretaris');
        $anggota = $this->buatUser('anggota');
        $agenda  = $this->buatAgenda();

        // Catat izin dua kali untuk user yang sama
        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->callAction('catat_izin', data: ['user_ids' => [$anggota->id]]);

        Livewire::actingAs($admin)
            ->test(ViewAgenda::class, ['record' => $agenda->getKey()])
            ->callAction('catat_izin', data: ['user_ids' => [$anggota->id]]);

        // Hanya 1 record presensi
        $this->assertDatabaseCount('presensis', 1);
    }

    public function test_agenda_auto_generate_qr_code_token_saat_dibuat(): void
    {
        $agenda = Agenda::create([
            'nama_agenda'   => 'Test Agenda',
            'deskripsi'     => 'Test',
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addHour(),
            'lokasi'        => 'Online',
            'is_active'     => true,
        ]);

        $this->assertNotNull($agenda->qr_code_token);
        $this->assertEquals(32, strlen($agenda->qr_code_token));
    }

    // ══════════════════════════════════════════════════════════════
    // REKAP PRESENSI PAGE — tidak ada canAccess override
    // ══════════════════════════════════════════════════════════════

    public function test_semua_role_panel_dapat_akses_rekap_presensi(): void
    {
        foreach (['super_admin', 'sekretaris', 'ketua_divisi'] as $role) {
            $admin = $this->buatUser($role);
            Livewire::actingAs($admin)
                ->test(RekapPresensi::class)
                ->assertSuccessful();
        }
    }

    public function test_bendahara_tidak_dapat_akses_rekap_presensi(): void
    {
        $admin = $this->buatUser('bendahara');
        Livewire::actingAs($admin)
            ->test(RekapPresensi::class)
            ->assertForbidden();
    }

    public function test_ketua_ukm_tidak_dapat_akses_rekap_presensi(): void
    {
        $admin = $this->buatUser('ketua_ukm');
        Livewire::actingAs($admin)
            ->test(RekapPresensi::class)
            ->assertForbidden();
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
}
