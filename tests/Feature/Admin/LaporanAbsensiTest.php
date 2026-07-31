<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Dashboard;
use App\Models\Agenda;
use App\Models\Presensi;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LaporanAbsensiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    private function seedDataAbsensi(): void
    {
        $anggota = $this->buatUser('anggota');
        $agenda = Agenda::create([
            'nama_agenda'   => 'Rapat Rutin',
            'deskripsi'     => 'Rapat rutin bulanan',
            'waktu_mulai'   => now(),
            'waktu_selesai' => now()->addHour(),
            'lokasi'        => 'Sekretariat',
            'is_active'     => true,
        ]);

        Presensi::create([
            'user_id'   => $anggota->id,
            'agenda_id' => $agenda->id,
            'jam_hadir' => now(),
            'status'    => 'Hadir',
        ]);
    }

    public function test_sekretaris_dapat_mengunduh_laporan_absensi(): void
    {
        $this->seedDataAbsensi();
        $admin = $this->buatUser('sekretaris');

        $this->actingAs($admin)->get(route('laporan-absensi.pdf'))->assertSuccessful();
    }

    public function test_super_admin_dapat_mengunduh_laporan_absensi(): void
    {
        $this->seedDataAbsensi();
        $admin = $this->buatUser('super_admin');

        $this->actingAs($admin)->get(route('laporan-absensi.pdf'))->assertSuccessful();
    }

    public function test_bendahara_tidak_dapat_mengunduh_laporan_absensi(): void
    {
        $admin = $this->buatUser('bendahara');

        $this->actingAs($admin)->get(route('laporan-absensi.pdf'))->assertForbidden();
    }

    public function test_ketua_ukm_dapat_mengunduh_laporan_absensi(): void
    {
        $this->seedDataAbsensi();
        $admin = $this->buatUser('ketua_ukm');

        $this->actingAs($admin)->get(route('laporan-absensi.pdf'))->assertSuccessful();
    }

    public function test_tombol_generate_laporan_tampil_di_dashboard_untuk_sekretaris(): void
    {
        $admin = $this->buatUser('sekretaris');

        Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->assertSuccessful()
            ->assertSee('Generate Laporan');
    }
}
