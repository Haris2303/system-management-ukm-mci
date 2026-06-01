<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\ProgramKerjas\ProgramKerjaResource;
use App\Models\Divisi;
use App\Models\Post;
use App\Models\ProgramKerja;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PostBeritaAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — PostResource (kelola_berita)
    // ══════════════════════════════════════════════════════════════

    public function test_sekretaris_dapat_akses_resource_berita(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertTrue(PostResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_akses_resource_berita(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(PostResource::canViewAny());
    }

    public function test_super_admin_dapat_akses_resource_berita(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(PostResource::canViewAny());
    }

    public function test_bendahara_tidak_dapat_akses_resource_berita(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(PostResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_akses_resource_berita(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(PostResource::canViewAny());
    }

    public function test_list_posts_dapat_diakses_sekretaris(): void
    {
        $admin = $this->buatUser('sekretaris');

        Livewire::actingAs($admin)
            ->test(ListPosts::class)
            ->assertSuccessful();
    }

    public function test_list_posts_menampilkan_semua_post(): void
    {
        $admin = $this->buatUser('sekretaris');
        $p1    = $this->buatPost(['judul' => 'Berita A']);
        $p2    = $this->buatPost(['judul' => 'Berita B']);

        Livewire::actingAs($admin)
            ->test(ListPosts::class)
            ->assertCanSeeTableRecords([$p1, $p2]);
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL Post — slug, published_at, scope
    // ══════════════════════════════════════════════════════════════

    public function test_post_auto_generate_slug_dari_judul(): void
    {
        $post = $this->buatPost(['judul' => 'Rapat Divisi Web Desember 2025']);

        $this->assertNotNull($post->slug);
        $this->assertEquals('rapat-divisi-web-desember-2025', $post->slug);
    }

    public function test_post_slug_unik_jika_judul_sama(): void
    {
        $post1 = $this->buatPost(['judul' => 'Berita Sama']);
        $post2 = $this->buatPost(['judul' => 'Berita Sama']);

        $this->assertNotEquals($post1->slug, $post2->slug);
        $this->assertEquals('berita-sama',   $post1->slug);
        $this->assertEquals('berita-sama-1', $post2->slug);
    }

    public function test_published_at_otomatis_diisi_saat_status_published(): void
    {
        $post = $this->buatPost(['status' => 'published']);

        $this->assertNotNull($post->published_at);
    }

    public function test_published_at_tidak_diisi_untuk_draft(): void
    {
        $post = $this->buatPost(['status' => 'draft']);

        $this->assertNull($post->published_at);
    }

    public function test_published_at_diisi_saat_status_diubah_ke_published(): void
    {
        $post = $this->buatPost(['status' => 'draft']);
        $this->assertNull($post->published_at);

        $post->update(['status' => 'published']);

        $this->assertNotNull($post->fresh()->published_at);
    }

    public function test_scope_published_hanya_mengembalikan_post_published(): void
    {
        $this->buatPost(['status' => 'draft']);
        $published = $this->buatPost(['status' => 'published']);

        $result = Post::published()->get();

        $this->assertCount(1, $result);
        $this->assertEquals($published->id, $result->first()->id);
    }

    public function test_tag_disimpan_sebagai_string_dan_dibaca_sebagai_array(): void
    {
        $post = $this->buatPost(['tag' => ['teknologi', 'coding', 'web']]);

        $tags = $post->getTags();
        $this->assertIsArray($tags);
        $this->assertContains('teknologi', $tags);
        $this->assertContains('coding',    $tags);
    }

    public function test_tag_kosong_mengembalikan_array_kosong(): void
    {
        $post = $this->buatPost(['tag' => null]);
        $this->assertIsArray($post->getTags());
        $this->assertEmpty($post->getTags());
    }

    public function test_read_time_menghitung_estimasi_menit(): void
    {
        // 200 kata → 1 menit
        $post = $this->buatPost(['konten' => implode(' ', array_fill(0, 200, 'kata'))]);
        $this->assertEquals('1 menit baca', $post->readTime());
    }

    // ══════════════════════════════════════════════════════════════
    // PROGRAM KERJA — query scoping berdasarkan role
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_melihat_semua_proker(): void
    {
        $divisi = $this->buatDivisi();
        $admin  = $this->buatUser('super_admin');

        $this->buatProker();
        $this->buatProker(['divisi_id' => $divisi->id, 'nama_proker' => 'Proker Divisi']);

        $this->actingAs($admin);
        $this->assertEquals(2, ProgramKerjaResource::getEloquentQuery()->count());
    }

    public function test_ketua_ukm_melihat_semua_proker(): void
    {
        $divisi = $this->buatDivisi();
        $admin  = $this->buatUser('ketua_ukm');

        $this->buatProker();
        $this->buatProker(['divisi_id' => $divisi->id, 'nama_proker' => 'Divisi']);

        $this->actingAs($admin);
        $this->assertEquals(2, ProgramKerjaResource::getEloquentQuery()->count());
    }

    public function test_sekretaris_melihat_proker_umum_dan_divisinya(): void
    {
        $divisiA   = $this->buatDivisi('A');
        $divisiB   = $this->buatDivisi('B');
        $sekretaris = $this->buatUser('sekretaris', ['divisi_id' => $divisiA->id]);

        $this->buatProker(['nama_proker' => 'Umum']);
        $this->buatProker(['divisi_id' => $divisiA->id, 'nama_proker' => 'Divisi A']);
        $this->buatProker(['divisi_id' => $divisiB->id, 'nama_proker' => 'Divisi B']);

        $this->actingAs($sekretaris);
        $result = ProgramKerjaResource::getEloquentQuery()->pluck('nama_proker');

        $this->assertCount(2, $result);
        $this->assertContains('Umum',    $result);
        $this->assertContains('Divisi A', $result);
        $this->assertNotContains('Divisi B', $result);
    }

    public function test_navigation_badge_proker_menampilkan_jumlah_yang_aktif(): void
    {
        $admin = $this->buatUser('super_admin');
        $this->actingAs($admin);

        $this->buatProker(['status' => 'active']);
        $this->buatProker(['status' => 'active',    'nama_proker' => 'Aktif 2']);
        $this->buatProker(['status' => 'planning',  'nama_proker' => 'Planning']);
        $this->buatProker(['status' => 'completed', 'nama_proker' => 'Selesai', 'progress_persen' => 100]);

        $badge = ProgramKerjaResource::getNavigationBadge();
        $this->assertEquals('2', $badge);
    }
}
