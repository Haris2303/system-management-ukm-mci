<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Models\Gallery;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModelGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_semua_role_panel_dapat_akses_galeri(): void
    {
        foreach (['super_admin', 'ketua_ukm', 'sekretaris', 'bendahara', 'ketua_divisi'] as $role) {
            $admin = $this->buatUser($role);
            Livewire::actingAs($admin)
                ->test(ListGalleries::class)
                ->assertSuccessful();
        }
    }

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
}
