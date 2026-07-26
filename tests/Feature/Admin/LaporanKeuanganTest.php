<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Dashboard;
use App\Models\TagihanKas;
use App\Models\TransaksiKas;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LaporanKeuanganTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    private function seedDataKas(): void
    {
        $anggota = $this->buatUser('anggota');

        TagihanKas::create([
            'user_id'       => $anggota->id,
            'bulan_tagihan' => '2026-01',
            'nominal'       => 50000,
            'status'        => 'lunas',
            'tanggal_bayar' => now(),
        ]);

        TransaksiKas::create([
            'jenis'      => 'masuk',
            'nominal'    => 100000,
            'keterangan' => 'Donasi',
            'tanggal'    => now(),
        ]);
    }

    public function test_bendahara_dapat_mengunduh_laporan(): void
    {
        $this->seedDataKas();
        $admin = $this->buatUser('bendahara');

        $this->actingAs($admin)->get(route('laporan-keuangan.pdf'))->assertSuccessful();
    }

    public function test_ketua_divisi_tidak_dapat_mengunduh_laporan(): void
    {
        $admin = $this->buatUser('ketua_divisi');

        $this->actingAs($admin)->get(route('laporan-keuangan.pdf'))->assertForbidden();
    }

    public function test_ketua_ukm_tidak_dapat_mengunduh_laporan(): void
    {
        $admin = $this->buatUser('ketua_ukm');

        $this->actingAs($admin)->get(route('laporan-keuangan.pdf'))->assertForbidden();
    }

    public function test_tombol_generate_laporan_tampil_di_dashboard_untuk_bendahara(): void
    {
        $admin = $this->buatUser('bendahara');

        Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->assertSuccessful()
            ->assertSee('Generate Laporan');
    }

    public function test_tombol_generate_laporan_tidak_tampil_di_dashboard_untuk_ketua_ukm(): void
    {
        $admin = $this->buatUser('ketua_ukm');

        Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->assertSuccessful()
            ->assertDontSee('Generate Laporan');
    }
}
