<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\OpenRecruitmentPage;
use App\Models\OpenRecruitment;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OpenRecruitmentAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ─── helpers ───────────────────────────────────────────────

    private function buatOR(array $attrs = []): OpenRecruitment
    {
        return OpenRecruitment::create(array_merge([
            'judul'         => 'Open Recruitment 2025',
            'gelombang'     => 'Gelombang 1',
            'deskripsi'     => 'Deskripsi rekrutmen',
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addDays(7),
            'is_active'     => true,
        ], $attrs));
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — canAccess (hanya super_admin & ketua_ukm)
    // ══════════════════════════════════════════════════════════════

    public function test_super_admin_dapat_akses_halaman_open_recruitment(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(OpenRecruitmentPage::canAccess());
    }

    public function test_ketua_ukm_dapat_akses_halaman_open_recruitment(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(OpenRecruitmentPage::canAccess());
    }

    public function test_sekretaris_tidak_dapat_akses_halaman_open_recruitment(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(OpenRecruitmentPage::canAccess());
    }

    public function test_bendahara_tidak_dapat_akses_halaman_open_recruitment(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertFalse(OpenRecruitmentPage::canAccess());
    }

    public function test_ketua_divisi_tidak_dapat_akses_halaman_open_recruitment(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(OpenRecruitmentPage::canAccess());
    }

    // ══════════════════════════════════════════════════════════════
    // LIVEWIRE PAGE — render & simpan
    // ══════════════════════════════════════════════════════════════

    public function test_halaman_dapat_diakses_ketua_ukm(): void
    {
        $admin = $this->buatUser('ketua_ukm');

        Livewire::actingAs($admin)
            ->test(OpenRecruitmentPage::class)
            ->assertSuccessful();
    }

    public function test_simpan_data_rekrutmen_baru(): void
    {
        $admin = $this->buatUser('ketua_ukm');

        Livewire::actingAs($admin)
            ->test(OpenRecruitmentPage::class)
            ->fillForm([
                'judul'         => 'OR 2026',
                'gelombang'     => 'Gelombang 1',
                'deskripsi'     => 'Rekrutmen tahun baru',
                'waktu_mulai'   => now()->subHour()->format('Y-m-d H:i'),
                'waktu_selesai' => now()->addDays(14)->format('Y-m-d H:i'),
                'is_active'     => true,
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('open_recruitments', ['judul' => 'OR 2026']);
    }

    public function test_simpan_membuat_rekrutmen_baru_jika_belum_ada(): void
    {
        $admin = $this->buatUser('ketua_ukm');

        $this->assertDatabaseCount('open_recruitments', 0);

        Livewire::actingAs($admin)
            ->test(OpenRecruitmentPage::class)
            ->fillForm([
                'judul'         => 'OR Test',
                'gelombang'     => 'Gelombang 1',
                'waktu_mulai'   => now()->subHour()->format('Y-m-d H:i'),
                'waktu_selesai' => now()->addDays(7)->format('Y-m-d H:i'),
                'is_active'     => false,
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('open_recruitments', 1);
    }

    public function test_simpan_memperbarui_rekrutmen_yang_sudah_ada(): void
    {
        $this->buatOR(['judul' => 'OR Lama']);
        $admin = $this->buatUser('ketua_ukm');

        Livewire::actingAs($admin)
            ->test(OpenRecruitmentPage::class)
            ->fillForm([
                'judul'         => 'OR Baru',
                'gelombang'     => 'Gelombang 2',
                'waktu_mulai'   => now()->subHour()->format('Y-m-d H:i'),
                'waktu_selesai' => now()->addDays(14)->format('Y-m-d H:i'),
                'is_active'     => true,
            ])
            ->call('save')
            ->assertHasNoErrors();

        // Tetap 1 record, bukan membuat baru
        $this->assertDatabaseCount('open_recruitments', 1);
        $this->assertDatabaseHas('open_recruitments', ['judul' => 'OR Baru']);
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL OpenRecruitment
    // ══════════════════════════════════════════════════════════════

    public function test_is_sedang_berlangsung_true_jika_aktif_dan_dalam_rentang_waktu(): void
    {
        $or = $this->buatOR([
            'is_active'     => true,
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addHours(5),
        ]);

        $this->assertTrue($or->isSedangBerlangsung());
    }

    public function test_is_sedang_berlangsung_false_jika_tidak_aktif(): void
    {
        $or = $this->buatOR([
            'is_active'     => false,
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addHours(5),
        ]);

        $this->assertFalse($or->isSedangBerlangsung());
    }

    public function test_is_sedang_berlangsung_false_jika_belum_mulai(): void
    {
        $or = $this->buatOR([
            'is_active'     => true,
            'waktu_mulai'   => now()->addHour(),
            'waktu_selesai' => now()->addDays(7),
        ]);

        $this->assertFalse($or->isSedangBerlangsung());
    }

    public function test_is_sedang_berlangsung_false_jika_sudah_selesai(): void
    {
        $or = $this->buatOR([
            'is_active'     => true,
            'waktu_mulai'   => now()->subDays(7),
            'waktu_selesai' => now()->subHour(),
        ]);

        $this->assertFalse($or->isSedangBerlangsung());
    }

    public function test_scope_active_mengembalikan_rekrutmen_yang_sedang_berlangsung(): void
    {
        $aktif = $this->buatOR([
            'is_active'     => true,
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addDays(7),
        ]);

        $this->buatOR([
            'is_active'     => false,
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addDays(7),
        ]);

        $this->buatOR([
            'is_active'     => true,
            'waktu_mulai'   => now()->subDays(7),
            'waktu_selesai' => now()->subHour(), // sudah selesai
        ]);

        $result = OpenRecruitment::active()->get();

        $this->assertCount(1, $result);
        $this->assertEquals($aktif->id, $result->first()->id);
    }
}
