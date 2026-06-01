<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\TagihanKas\Pages\ListTagihanKas;
use App\Filament\Resources\TagihanKas\TagihanKasResource;
use App\Models\TagihanKas;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TagihanKasAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ─── helpers ───────────────────────────────────────────────

    private function buatTagihan(User $user, string $bulan, string $status = 'belum_dibayar', int $nominal = 50000, array $extra = []): TagihanKas
    {
        return TagihanKas::create(array_merge([
            'user_id'       => $user->id,
            'bulan_tagihan' => $bulan,
            'nominal'       => $nominal,
            'status'        => $status,
        ], $extra));
    }

    // ══════════════════════════════════════════════════════════════
    // HAK AKSES — canViewAny
    // ══════════════════════════════════════════════════════════════

    public function test_bendahara_dapat_akses_tagihan_kas(): void
    {
        $this->actingAs($this->buatUser('bendahara'));
        $this->assertTrue(TagihanKasResource::canViewAny());
    }

    public function test_ketua_ukm_dapat_akses_tagihan_kas(): void
    {
        $this->actingAs($this->buatUser('ketua_ukm'));
        $this->assertTrue(TagihanKasResource::canViewAny());
    }

    public function test_super_admin_dapat_akses_tagihan_kas(): void
    {
        $this->actingAs($this->buatUser('super_admin'));
        $this->assertTrue(TagihanKasResource::canViewAny());
    }

    public function test_sekretaris_tidak_dapat_akses_tagihan_kas(): void
    {
        $this->actingAs($this->buatUser('sekretaris'));
        $this->assertFalse(TagihanKasResource::canViewAny());
    }

    public function test_ketua_divisi_tidak_dapat_akses_tagihan_kas(): void
    {
        $this->actingAs($this->buatUser('ketua_divisi'));
        $this->assertFalse(TagihanKasResource::canViewAny());
    }

    // ══════════════════════════════════════════════════════════════
    // LIST PAGE
    // ══════════════════════════════════════════════════════════════

    public function test_list_page_dapat_diakses_bendahara(): void
    {
        $bendahara = $this->buatUser('bendahara');

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->assertSuccessful();
    }

    public function test_list_page_menampilkan_semua_tagihan(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');

        $t1 = $this->buatTagihan($anggota, '2025-01');
        $t2 = $this->buatTagihan($anggota, '2025-02');

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->assertCanSeeTableRecords([$t1, $t2]);
    }

    // ══════════════════════════════════════════════════════════════
    // AKSI: TANDAI LUNAS
    // ══════════════════════════════════════════════════════════════

    public function test_aksi_tandai_lunas_tersedia_untuk_belum_dibayar(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');
        $tagihan   = $this->buatTagihan($anggota, '2025-01', 'belum_dibayar');

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->assertTableActionVisible('tandai_lunas', $tagihan);
    }

    public function test_aksi_tandai_lunas_tersembunyi_untuk_yang_sudah_lunas(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');
        $tagihan   = $this->buatTagihan($anggota, '2025-01', 'lunas', 50000, [
            'tanggal_bayar' => now(),
        ]);

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->assertTableActionHidden('tandai_lunas', $tagihan);
    }

    public function test_eksekusi_tandai_lunas_mengubah_status_ke_lunas(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');
        $tagihan   = $this->buatTagihan($anggota, '2025-01', 'belum_dibayar');

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->callTableAction('tandai_lunas', $tagihan)
            ->assertHasNoTableActionErrors();

        $this->assertEquals('lunas', $tagihan->fresh()->status);
        $this->assertNotNull($tagihan->fresh()->tanggal_bayar);
    }

    // ══════════════════════════════════════════════════════════════
    // AKSI: BATALKAN LUNAS
    // ══════════════════════════════════════════════════════════════

    public function test_aksi_batalkan_lunas_tersedia_untuk_yang_sudah_lunas(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');
        $tagihan   = $this->buatTagihan($anggota, '2025-01', 'lunas', 50000, [
            'tanggal_bayar' => now(),
        ]);

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->assertTableActionVisible('batalkan_lunas', $tagihan);
    }

    public function test_aksi_batalkan_lunas_tersembunyi_untuk_belum_dibayar(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');
        $tagihan   = $this->buatTagihan($anggota, '2025-01', 'belum_dibayar');

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->assertTableActionHidden('batalkan_lunas', $tagihan);
    }

    public function test_eksekusi_batalkan_lunas_mengembalikan_status_ke_belum_dibayar(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');
        $tagihan   = $this->buatTagihan($anggota, '2025-01', 'lunas', 50000, [
            'tanggal_bayar' => now()->subDay(),
        ]);

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->callTableAction('batalkan_lunas', $tagihan)
            ->assertHasNoTableActionErrors();

        $fresh = $tagihan->fresh();
        $this->assertEquals('belum_dibayar', $fresh->status);
        $this->assertNull($fresh->tanggal_bayar);
    }

    // ══════════════════════════════════════════════════════════════
    // BULK ACTION: TANDAI LUNAS MASSAL
    // ══════════════════════════════════════════════════════════════

    public function test_tandai_lunas_massal_menandai_semua_yang_belum_dibayar(): void
    {
        $bendahara = $this->buatUser('bendahara');
        $anggota   = $this->buatUser('anggota');

        $t1 = $this->buatTagihan($anggota, '2025-01', 'belum_dibayar');
        $t2 = $this->buatTagihan($anggota, '2025-02', 'belum_dibayar');
        $t3 = $this->buatTagihan($anggota, '2025-03', 'lunas', 50000, ['tanggal_bayar' => now()]);

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->callTableBulkAction('tandai_lunas_massal', [$t1, $t2, $t3])
            ->assertHasNoErrors();

        $this->assertEquals('lunas', $t1->fresh()->status);
        $this->assertEquals('lunas', $t2->fresh()->status);
        // t3 sudah lunas, tidak berubah (idempotent)
        $this->assertEquals('lunas', $t3->fresh()->status);
    }

    public function test_tandai_lunas_massal_skip_yang_sudah_lunas(): void
    {
        $bendahara     = $this->buatUser('bendahara');
        $anggota       = $this->buatUser('anggota');
        $sudahLunas    = $this->buatTagihan($anggota, '2025-01', 'lunas', 50000, [
            'tanggal_bayar' => now()->subDays(5),
        ]);

        $tanggalLama = $sudahLunas->tanggal_bayar;

        Livewire::actingAs($bendahara)
            ->test(ListTagihanKas::class)
            ->callTableBulkAction('tandai_lunas_massal', [$sudahLunas]);

        // Tanggal bayar tidak berubah karena sudah lunas
        $this->assertEquals(
            $tanggalLama->toDateString(),
            $sudahLunas->fresh()->tanggal_bayar->toDateString()
        );
    }

    // ══════════════════════════════════════════════════════════════
    // MODEL: markAsPaid & helpers
    // ══════════════════════════════════════════════════════════════

    public function test_mark_as_paid_mengubah_status_dan_mencatat_tanggal(): void
    {
        $anggota = $this->buatUser('anggota');
        $tagihan = $this->buatTagihan($anggota, '2025-01', 'belum_dibayar');

        $this->assertTrue($tagihan->isUnpaid());

        $tagihan->markAsPaid();

        $fresh = $tagihan->fresh();
        $this->assertTrue($fresh->isPaid());
        $this->assertNotNull($fresh->tanggal_bayar);
    }
}
