<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UsersAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — UserResource (kelola_users → hanya super_admin)
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_akses_manajemen_users(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(UserResource::canCreate());
    }

    public function test_ketua_ukm_tidak_dapat_akses_manajemen_users(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertFalse(UserResource::canViewAny());
    }

    public function test_list_page_dapat_diakses_super_admin(): void
    {
        $admin = $this->buatUser('super_admin');
        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertSuccessful();
    }

    // ══════════════════════════════════════════════════════════════
    // SELF-PROTECTION — tidak bisa hapus / kick diri sendiri
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_tidak_dapat_hapus_dirinya_sendiri(): void
    {
        $admin = $this->buatUser('super_admin');
        $this->actingAs($admin);
        $this->assertFalse(UserResource::canDelete($admin));
    }

    public function test_aksi_hapus_tersembunyi_untuk_diri_sendiri(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertActionVisible(TestAction::make('delete')->table($target))
            ->assertActionHidden(TestAction::make('delete')->table($admin));
    }

    public function test_aksi_demisionerkan_tersembunyi_untuk_diri_sendiri(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertActionVisible(TestAction::make('demisionerkan')->table($target))
            ->assertActionHidden(TestAction::make('demisionerkan')->table($admin));
    }

    public function test_aksi_kick_tersembunyi_untuk_diri_sendiri(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertActionVisible(TestAction::make('kick')->table($target))
            ->assertActionHidden(TestAction::make('kick')->table($admin));
    }

    // ══════════════════════════════════════════════════════════════
    // AKSI: RESET PASSWORD
    // ══════════════════════════════════════════════════════════════

    public function test_reset_password_mengubah_password_ke_default(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota', ['password' => 'passwordlama']);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callAction(TestAction::make('reset_password')->table($target))
            ->assertHasNoFormErrors();

        // Login dengan password baru harus berhasil
        $this->assertTrue(Hash::check('password123', $target->fresh()->password));
    }

    // ══════════════════════════════════════════════════════════════
    // AKSI: DEMISIONERKAN
    // ══════════════════════════════════════════════════════════════

    public function test_demisionerkan_mengubah_role_ke_demisioner(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callAction(TestAction::make('demisionerkan')->table($target))
            ->assertHasNoFormErrors();

        $this->assertTrue($target->fresh()->hasRole('demisioner'));
        $this->assertFalse($target->fresh()->hasRole('anggota'));
    }

    public function test_demisionerkan_menghapus_semua_token_sanctum(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');
        $target->createToken('mobile-token');

        $this->assertDatabaseCount('personal_access_tokens', 1);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callAction(TestAction::make('demisionerkan')->table($target));

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_aksi_demisionerkan_tersembunyi_untuk_yang_sudah_demisioner(): void
    {
        $admin      = $this->buatUser('super_admin');
        $demisioner = $this->buatUser('demisioner');

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertActionHidden(TestAction::make('demisionerkan')->table($demisioner));
    }

    // ══════════════════════════════════════════════════════════════
    // AKSI: KICK (KELUARKAN)
    // ══════════════════════════════════════════════════════════════

    public function test_kick_mencatat_kicked_at_dan_hapus_token(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');
        $target->createToken('mobile');

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callAction(TestAction::make('kick')->table($target), data: [
                'kicked_reason' => 'Melanggar aturan organisasi.',
            ])
            ->assertHasNoFormErrors();

        $fresh = $target->fresh();
        $this->assertNotNull($fresh->kicked_at);
        $this->assertEquals('Melanggar aturan organisasi.', $fresh->kicked_reason);
        $this->assertEquals($admin->id, $fresh->kicked_by);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_kick_tanpa_alasan_tetap_berhasil(): void
    {
        $admin  = $this->buatUser('super_admin');
        $target = $this->buatUser('anggota');

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callAction(TestAction::make('kick')->table($target), data: ['kicked_reason' => null])
            ->assertHasNoFormErrors();

        $this->assertNotNull($target->fresh()->kicked_at);
        $this->assertNull($target->fresh()->kicked_reason);
    }

    public function test_aksi_kick_tersembunyi_untuk_yang_sudah_di_kick(): void
    {
        $admin       = $this->buatUser('super_admin');
        $sudahDikick = $this->buatUser('anggota', ['kicked_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertActionHidden(TestAction::make('kick')->table($sudahDikick));
    }

    // ══════════════════════════════════════════════════════════════
    // AKSI: PULIHKAN KICK
    // ══════════════════════════════════════════════════════════════

    public function test_pulihkan_kick_menghapus_data_kick(): void
    {
        $admin       = $this->buatUser('super_admin');
        $sudahDikick = $this->buatUser('anggota', [
            'kicked_at'     => now()->subDays(3),
            'kicked_by'     => 1,
            'kicked_reason' => 'Alasan tertentu',
        ]);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callAction(TestAction::make('pulihkan_kick')->table($sudahDikick))
            ->assertHasNoFormErrors();

        $fresh = $sudahDikick->fresh();
        $this->assertNull($fresh->kicked_at);
        $this->assertNull($fresh->kicked_by);
        $this->assertNull($fresh->kicked_reason);
    }

    public function test_aksi_pulihkan_tersedia_hanya_untuk_yang_sudah_di_kick(): void
    {
        $admin    = $this->buatUser('super_admin');
        $normal   = $this->buatUser('anggota');
        $dikick   = $this->buatUser('anggota', ['kicked_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertActionHidden(TestAction::make('pulihkan_kick')->table($normal))
            ->assertActionVisible(TestAction::make('pulihkan_kick')->table($dikick));
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL User — isKicked & isAccountActive
    // ══════════════════════════════════════════════════════════════

    public function test_is_kicked_true_jika_ada_kicked_at(): void
    {
        $user = User::factory()->create(['kicked_at' => now()]);
        $this->assertTrue($user->isKicked());
    }

    public function test_is_kicked_false_jika_tidak_ada_kicked_at(): void
    {
        $user = User::factory()->create(['kicked_at' => null]);
        $this->assertFalse($user->isKicked());
    }

    public function test_is_account_active_false_untuk_demisioner(): void
    {
        $user = $this->buatUser('demisioner');
        $this->assertFalse($user->isAccountActive());
    }
}
