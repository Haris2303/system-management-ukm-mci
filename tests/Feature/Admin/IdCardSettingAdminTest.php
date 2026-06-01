<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\IdCardSettingPage;
use App\Models\IdCardSetting;
use App\Models\User;
use App\Support\IdCardTemplates;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class IdCardSettingAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);

        Storage::fake('public');
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — canAccess (hanya super_admin)
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_akses_id_card_setting(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(IdCardSettingPage::canAccess());
    }

    public function test_ketua_ukm_tidak_dapat_akses_id_card_setting(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertFalse(IdCardSettingPage::canAccess());
    }

    public function test_sekretaris_tidak_dapat_akses_id_card_setting(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(IdCardSettingPage::canAccess());
    }

    public function test_bendahara_tidak_dapat_akses_id_card_setting(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(IdCardSettingPage::canAccess());
    }

    public function test_ketua_divisi_tidak_dapat_akses_id_card_setting(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(IdCardSettingPage::canAccess());
    }

    // ══════════════════════════════════════════════════════════════
    // LIVEWIRE PAGE — render & mount
    // ══════════════════════════════════════════════════════════════

    public function test_halaman_dapat_diakses_super_admin(): void
    {
        $admin = $this->buatUser('super_admin');

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->assertSuccessful();
    }

    public function test_mount_memuat_template_aktif_dari_database(): void
    {
        $admin = $this->buatUser('super_admin');
        IdCardSetting::current()->update(['border_template' => 'teknologi']);

        $component = Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class);

        $this->assertEquals('teknologi', $component->get('activeTemplate'));
    }

    public function test_mount_memuat_background_image_dari_database(): void
    {
        $admin = $this->buatUser('super_admin');
        IdCardSetting::current()->update(['background_image' => 'id-card-backgrounds/bg.jpg']);

        $component = Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class);

        $this->assertNotNull($component->get('backgroundImage'));
        $this->assertStringContainsString('id-card-backgrounds/bg.jpg', $component->get('backgroundImage'));
    }

    public function test_mount_background_null_jika_belum_diset(): void
    {
        $admin = $this->buatUser('super_admin');
        IdCardSetting::current()->update(['background_image' => null]);

        $component = Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class);

        $this->assertNull($component->get('backgroundImage'));
    }

    // ══════════════════════════════════════════════════════════════
    // selectTemplate — pilih template ID card
    // ══════════════════════════════════════════════════════════════

    public function test_select_template_mengubah_template_aktif_di_database(): void
    {
        $admin = $this->buatUser('super_admin');

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->call('selectTemplate', 'teknologi');

        $this->assertEquals('teknologi', IdCardSetting::activeTemplate());
    }

    public function test_select_template_mengupdate_property_active_template(): void
    {
        $admin = $this->buatUser('super_admin');

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->call('selectTemplate', 'elegan-emas')
            ->assertSet('activeTemplate', 'elegan-emas');
    }

    public function test_select_template_mencatat_updated_by(): void
    {
        $admin = $this->buatUser('super_admin');

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->call('selectTemplate', 'ungu-gradien');

        $this->assertEquals($admin->id, IdCardSetting::current()->updated_by);
    }

    public function test_semua_template_dapat_dipilih(): void
    {
        $admin  = $this->buatUser('super_admin');
        $slugs  = array_keys(IdCardTemplates::all());

        foreach ($slugs as $slug) {
            Livewire::actingAs($admin)
                ->test(IdCardSettingPage::class)
                ->call('selectTemplate', $slug);

            $this->assertEquals($slug, IdCardSetting::activeTemplate(), "Gagal set template: {$slug}");
        }
    }

    // ══════════════════════════════════════════════════════════════
    // removeBackground — hapus background image
    // ══════════════════════════════════════════════════════════════

    public function test_remove_background_tersedia_jika_ada_background(): void
    {
        $admin = $this->buatUser('super_admin');
        IdCardSetting::current()->update(['background_image' => 'id-card-backgrounds/bg.jpg']);

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->assertActionVisible('removeBackground');
    }

    public function test_remove_background_tersembunyi_jika_tidak_ada_background(): void
    {
        $admin = $this->buatUser('super_admin');
        IdCardSetting::current()->update(['background_image' => null]);

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->assertActionHidden('removeBackground');
    }

    public function test_eksekusi_remove_background_menghapus_dari_database(): void
    {
        $admin = $this->buatUser('super_admin');
        IdCardSetting::current()->update(['background_image' => 'id-card-backgrounds/bg.jpg']);

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->callAction('removeBackground')
            ->assertHasNoActionErrors();

        $this->assertNull(IdCardSetting::current()->fresh()->background_image);
    }

    public function test_eksekusi_remove_background_mengupdate_property_jadi_null(): void
    {
        $admin = $this->buatUser('super_admin');
        IdCardSetting::current()->update(['background_image' => 'id-card-backgrounds/bg.jpg']);

        Livewire::actingAs($admin)
            ->test(IdCardSettingPage::class)
            ->callAction('removeBackground')
            ->assertSet('backgroundImage', null);
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL IdCardSetting
    // ══════════════════════════════════════════════════════════════

    public function test_current_membuat_row_default_jika_belum_ada(): void
    {
        // Kosongkan tabel
        IdCardSetting::truncate();
        $this->assertDatabaseCount('id_card_settings', 0);

        $setting = IdCardSetting::current();

        $this->assertDatabaseCount('id_card_settings', 1);
        $this->assertEquals('biru-klasik', $setting->border_template);
    }

    public function test_current_tidak_duplikat_jika_sudah_ada(): void
    {
        IdCardSetting::current(); // panggil pertama kali
        IdCardSetting::current(); // panggil kedua kali

        $this->assertDatabaseCount('id_card_settings', 1);
    }

    public function test_active_template_mengembalikan_template_saat_ini(): void
    {
        IdCardSetting::current()->update(['border_template' => 'hijau-alam']);

        $this->assertEquals('hijau-alam', IdCardSetting::activeTemplate());
    }

    public function test_set_template_mengubah_border_template(): void
    {
        $admin = $this->buatUser('super_admin');
        $this->actingAs($admin);

        IdCardSetting::setTemplate('merah-energi');

        $this->assertEquals('merah-energi', IdCardSetting::current()->border_template);
    }

    public function test_set_background_image_menyimpan_path(): void
    {
        $admin = $this->buatUser('super_admin');
        $this->actingAs($admin);

        IdCardSetting::setBackgroundImage('id-card-backgrounds/custom.jpg');

        $this->assertEquals(
            'id-card-backgrounds/custom.jpg',
            IdCardSetting::current()->background_image
        );
    }

    public function test_set_background_image_null_menghapus_path(): void
    {
        $admin = $this->buatUser('super_admin');
        $this->actingAs($admin);

        IdCardSetting::setBackgroundImage('id-card-backgrounds/custom.jpg');
        IdCardSetting::setBackgroundImage(null);

        $this->assertNull(IdCardSetting::current()->background_image);
    }

    public function test_background_image_url_null_jika_tidak_ada_path(): void
    {
        IdCardSetting::current()->update(['background_image' => null]);

        $this->assertNull(IdCardSetting::backgroundImageUrl());
    }

    public function test_background_image_url_berisi_path_jika_ada(): void
    {
        IdCardSetting::current()->update(['background_image' => 'id-card-backgrounds/bg.jpg']);

        $url = IdCardSetting::backgroundImageUrl();

        $this->assertNotNull($url);
        $this->assertStringContainsString('id-card-backgrounds/bg.jpg', $url);
    }

    public function test_editor_relationship_ke_user(): void
    {
        $admin   = $this->buatUser('super_admin');
        $setting = IdCardSetting::current();
        $setting->update(['updated_by' => $admin->id]);

        $this->assertEquals($admin->id, $setting->fresh()->editor->id);
    }

    // ══════════════════════════════════════════════════════════════
    // SUPPORT IdCardTemplates
    // ══════════════════════════════════════════════════════════════

    public function test_all_mengembalikan_6_template(): void
    {
        $templates = IdCardTemplates::all();

        $this->assertCount(6, $templates);
    }

    public function test_all_memiliki_semua_slug_yang_diharapkan(): void
    {
        $slugs = array_keys(IdCardTemplates::all());

        $this->assertContains('biru-klasik',  $slugs);
        $this->assertContains('teknologi',    $slugs);
        $this->assertContains('elegan-emas',  $slugs);
        $this->assertContains('ungu-gradien', $slugs);
        $this->assertContains('merah-energi', $slugs);
        $this->assertContains('hijau-alam',   $slugs);
    }

    public function test_setiap_template_memiliki_slug_label_preview_bg_dan_css(): void
    {
        foreach (IdCardTemplates::all() as $slug => $template) {
            $this->assertArrayHasKey('slug',       $template, "Template {$slug} tidak punya 'slug'");
            $this->assertArrayHasKey('label',      $template, "Template {$slug} tidak punya 'label'");
            $this->assertArrayHasKey('preview_bg', $template, "Template {$slug} tidak punya 'preview_bg'");
            $this->assertArrayHasKey('css',        $template, "Template {$slug} tidak punya 'css'");
            $this->assertEquals($slug, $template['slug']);
        }
    }

    public function test_find_mengembalikan_template_berdasarkan_slug(): void
    {
        $template = IdCardTemplates::find('teknologi');

        $this->assertEquals('teknologi',  $template['slug']);
        $this->assertEquals('Teknologi',  $template['label']);
    }

    public function test_find_fallback_ke_biru_klasik_jika_slug_tidak_ditemukan(): void
    {
        $template = IdCardTemplates::find('slug-tidak-ada');

        $this->assertEquals('biru-klasik', $template['slug']);
    }

    public function test_options_mengembalikan_map_slug_ke_label(): void
    {
        $options = IdCardTemplates::options();

        $this->assertArrayHasKey('biru-klasik', $options);
        $this->assertEquals('Biru Klasik', $options['biru-klasik']);
        $this->assertEquals('Teknologi',   $options['teknologi']);
        $this->assertCount(6, $options);
    }

    public function test_setiap_css_template_mengandung_class_id_card(): void
    {
        foreach (IdCardTemplates::all() as $slug => $template) {
            $this->assertStringContainsString(
                '.id-card',
                $template['css'],
                "CSS template {$slug} tidak mendefinisikan .id-card"
            );
        }
    }
}
