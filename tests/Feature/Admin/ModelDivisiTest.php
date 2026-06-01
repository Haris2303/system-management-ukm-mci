<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Divisis\DivisiResource;
use App\Models\Divisi;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModelDivisiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ══════════════════════════════════════════════════════════════
    // DIVISI RESOURCE — kelola_divisi (super_admin & ketua_ukm)
    // ══════════════════════════════════════════════════════════════

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
}
