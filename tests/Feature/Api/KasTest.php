<?php

namespace Tests\Feature\Api;

use App\Models\TagihanKas;
use App\Models\TransaksiKas;
use App\Models\User;
use App\Services\KasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'anggota',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'demisioner', 'guard_name' => 'web']);
    }

    // ─── helpers ───────────────────────────────────────────────

    private function demisionerUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('demisioner');
        return $user;
    }

    private function buatTagihan(User $user, string $bulan, string $status = 'belum_dibayar', int $nominal = 50000, array $extra = []): TagihanKas
    {
        return TagihanKas::create(array_merge([
            'user_id'       => $user->id,
            'bulan_tagihan' => $bulan,
            'nominal'       => $nominal,
            'status'        => $status,
        ], $extra));
    }

    private function buatTransaksi(string $jenis, int $nominal, ?string $tanggal = null): TransaksiKas
    {
        return TransaksiKas::create([
            'jenis'       => $jenis,
            'nominal'     => $nominal,
            'keterangan'  => "Test transaksi {$jenis}",
            'tanggal'     => $tanggal ?? today()->toDateString(),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // TUNGGAKAN — GET /api/kas/tunggakan
    // ══════════════════════════════════════════════════════════════

    public function test_tunggakan_mengembalikan_tagihan_belum_dibayar_milik_user(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'belum_dibayar', 50000);
        $this->buatTagihan($user, '2025-02', 'belum_dibayar', 50000);

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk()
            ->assertJsonPath('data.jumlah_tunggakan', 2)
            ->assertJsonPath('data.total_nominal', 100000)
            ->assertJsonCount(2, 'data.tagihan');
    }

    public function test_tunggakan_tidak_mengembalikan_tagihan_yang_sudah_lunas(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'belum_dibayar');
        $this->buatTagihan($user, '2025-02', 'lunas');

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        $this->assertEquals(1, $response->json('data.jumlah_tunggakan'));
        $this->assertEquals('belum_dibayar', $response->json('data.tagihan.0.status'));
    }

    public function test_tunggakan_tidak_mengembalikan_tagihan_user_lain(): void
    {
        $userA = $this->aktifUser();
        $userB = $this->aktifUser();

        $this->buatTagihan($userA, '2025-01');
        $this->buatTagihan($userB, '2025-01');

        $response = $this->withToken($this->tokenFor($userA))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        // Hanya 1 tagihan milik userA yang dikembalikan, bukan milik userB
        $this->assertEquals(1, $response->json('data.jumlah_tunggakan'));
    }

    public function test_tunggakan_diurutkan_berdasarkan_bulan_terlama_dahulu(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-03');
        $this->buatTagihan($user, '2025-01');
        $this->buatTagihan($user, '2025-02');

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        $tagihan = $response->json('data.tagihan');
        $this->assertEquals('2025-01', $tagihan[0]['bulan_tagihan']);
        $this->assertEquals('2025-02', $tagihan[1]['bulan_tagihan']);
        $this->assertEquals('2025-03', $tagihan[2]['bulan_tagihan']);
    }

    public function test_tunggakan_total_nominal_dihitung_dengan_benar(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'belum_dibayar', 50000);
        $this->buatTagihan($user, '2025-02', 'belum_dibayar', 75000);
        $this->buatTagihan($user, '2025-03', 'belum_dibayar', 25000);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        $this->assertEquals(150000, $response->json('data.total_nominal'));
        $this->assertEquals('Rp 150.000', $response->json('data.total_format'));
    }

    public function test_tunggakan_format_nominal_menggunakan_format_rupiah(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'belum_dibayar', 1500000);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        $this->assertEquals('Rp 1.500.000', $response->json('data.tagihan.0.nominal_format'));
    }

    public function test_tunggakan_format_bulan_menggunakan_nama_bulan_indonesia(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01');

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        $this->assertEquals('Januari 2025', $response->json('data.tagihan.0.bulan_tagihan_format'));
    }

    public function test_tunggakan_pesan_khusus_jika_tidak_ada_tunggakan(): void
    {
        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        $this->assertStringContainsString('tidak memiliki tunggakan', $response->json('pesan'));
        $this->assertEquals(0, $response->json('data.jumlah_tunggakan'));
        $this->assertEquals(0, $response->json('data.total_nominal'));
    }

    public function test_tunggakan_pesan_menyebut_jumlah_tagihan(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01');
        $this->buatTagihan($user, '2025-02');

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertOk();

        $this->assertStringContainsString('2', $response->json('pesan'));
    }

    // ── Hak Akses: Tunggakan ────────────────────────────────────

    public function test_tunggakan_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/kas/tunggakan')->assertUnauthorized();
    }

    public function test_tunggakan_diblokir_untuk_demisioner(): void
    {
        $user = $this->demisionerUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/tunggakan')
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // RIWAYAT — GET /api/kas/riwayat
    // ══════════════════════════════════════════════════════════════

    public function test_riwayat_mengembalikan_tagihan_lunas_milik_user(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'lunas', 50000, [
            'tanggal_bayar' => now()->subDays(5),
        ]);
        $this->buatTagihan($user, '2025-02', 'lunas', 50000, [
            'tanggal_bayar' => now()->subDays(2),
        ]);

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/riwayat')
            ->assertOk()
            ->assertJsonPath('data.jumlah_pembayaran', 2)
            ->assertJsonPath('data.total_dibayar', 100000)
            ->assertJsonCount(2, 'data.riwayat');
    }

    public function test_riwayat_tidak_mengembalikan_tagihan_belum_dibayar(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'lunas', 50000, ['tanggal_bayar' => now()]);
        $this->buatTagihan($user, '2025-02', 'belum_dibayar');

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/riwayat')
            ->assertOk();

        $this->assertEquals(1, $response->json('data.jumlah_pembayaran'));
        $this->assertEquals('lunas', $response->json('data.riwayat.0.status'));
    }

    public function test_riwayat_tidak_mengembalikan_tagihan_user_lain(): void
    {
        $userA = $this->aktifUser();
        $userB = $this->aktifUser();

        $this->buatTagihan($userA, '2025-01', 'lunas', 50000, ['tanggal_bayar' => now()]);
        $this->buatTagihan($userB, '2025-01', 'lunas', 50000, ['tanggal_bayar' => now()]);

        $response = $this->withToken($this->tokenFor($userA))
            ->getJson('/api/kas/riwayat')
            ->assertOk();

        $this->assertEquals(1, $response->json('data.jumlah_pembayaran'));
    }

    public function test_riwayat_diurutkan_berdasarkan_tanggal_bayar_terbaru(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'lunas', 50000, ['tanggal_bayar' => now()->subDays(10)]);
        $this->buatTagihan($user, '2025-02', 'lunas', 50000, ['tanggal_bayar' => now()->subDay()]);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/riwayat')
            ->assertOk();

        $riwayat = $response->json('data.riwayat');
        $this->assertEquals('2025-02', $riwayat[0]['bulan_tagihan']);
        $this->assertEquals('2025-01', $riwayat[1]['bulan_tagihan']);
    }

    public function test_riwayat_menyertakan_tanggal_bayar_format(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-06', 'lunas', 50000, [
            'tanggal_bayar' => now(),
        ]);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/riwayat')
            ->assertOk();

        $this->assertNotNull($response->json('data.riwayat.0.tanggal_bayar_format'));
        $this->assertNotNull($response->json('data.riwayat.0.tanggal_bayar'));
    }

    public function test_riwayat_total_dibayar_dihitung_dengan_benar(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'lunas', 50000, ['tanggal_bayar' => now()]);
        $this->buatTagihan($user, '2025-02', 'lunas', 75000, ['tanggal_bayar' => now()]);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/riwayat')
            ->assertOk();

        $this->assertEquals(125000, $response->json('data.total_dibayar'));
        $this->assertEquals('Rp 125.000', $response->json('data.total_dibayar_format'));
    }

    public function test_riwayat_pesan_kosong_jika_belum_ada_pembayaran(): void
    {
        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/riwayat')
            ->assertOk();

        $this->assertStringContainsString('belum memiliki riwayat', $response->json('pesan'));
        $this->assertEquals(0, $response->json('data.jumlah_pembayaran'));
    }

    // ── Hak Akses: Riwayat ─────────────────────────────────────

    public function test_riwayat_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/kas/riwayat')->assertUnauthorized();
    }

    public function test_riwayat_diblokir_untuk_demisioner(): void
    {
        $user = $this->demisionerUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/riwayat')
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // SALDO TRANSPARANSI — GET /api/kas/saldo-transparansi
    // ══════════════════════════════════════════════════════════════

    public function test_saldo_mengembalikan_total_yang_benar(): void
    {
        $user = $this->aktifUser();

        // Iuran lunas: Rp 200.000
        $this->buatTagihan($user, '2025-01', 'lunas', 100000, ['tanggal_bayar' => now()]);
        $this->buatTagihan($user, '2025-02', 'lunas', 100000, ['tanggal_bayar' => now()]);

        // Kas masuk: Rp 300.000
        $this->buatTransaksi('masuk', 300000);

        // Kas keluar: Rp 150.000
        $this->buatTransaksi('keluar', 150000);

        // Total = 200.000 + 300.000 - 150.000 = 350.000
        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/saldo-transparansi')
            ->assertOk();

        $this->assertEquals(350000, $response->json('data.total_saldo'));
        $this->assertEquals('Rp 350.000', $response->json('data.total_saldo_format'));
    }

    public function test_saldo_rincian_iuran_lunas_benar(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'lunas', 50000, ['tanggal_bayar' => now()]);
        $this->buatTagihan($user, '2025-02', 'belum_dibayar', 50000);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/saldo-transparansi')
            ->assertOk();

        // Hanya tagihan lunas yang masuk ke iuran_lunas
        $this->assertEquals(50000, $response->json('data.rincian.iuran_lunas.nominal'));
    }

    public function test_saldo_rincian_kas_masuk_benar(): void
    {
        $user = $this->aktifUser();
        $this->buatTransaksi('masuk', 200000);
        $this->buatTransaksi('masuk', 300000);
        $this->buatTransaksi('keluar', 100000);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/saldo-transparansi')
            ->assertOk();

        $this->assertEquals(500000, $response->json('data.rincian.kas_masuk.nominal'));
        $this->assertEquals(100000, $response->json('data.rincian.kas_keluar.nominal'));
    }

    public function test_saldo_nol_jika_tidak_ada_transaksi(): void
    {
        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/saldo-transparansi')
            ->assertOk();

        $this->assertEquals(0, $response->json('data.total_saldo'));
        $this->assertEquals('Rp 0', $response->json('data.total_saldo_format'));
    }

    public function test_saldo_struktur_response_lengkap(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/saldo-transparansi')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'total_saldo',
                    'total_saldo_format',
                    'rincian' => [
                        'iuran_lunas' => ['nominal', 'format'],
                        'kas_masuk'   => ['nominal', 'format'],
                        'kas_keluar'  => ['nominal', 'format'],
                    ],
                    'diperbarui_pada',
                ],
            ]);
    }

    // ── Hak Akses: Saldo ────────────────────────────────────────

    public function test_saldo_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/kas/saldo-transparansi')->assertUnauthorized();
    }

    public function test_saldo_diblokir_untuk_demisioner(): void
    {
        $user = $this->demisionerUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/saldo-transparansi')
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // KAS SERVICE — unit test kalkulasi
    // ══════════════════════════════════════════════════════════════

    public function test_kas_service_format_currency_angka_kecil(): void
    {
        $service = app(KasService::class);
        $this->assertEquals('Rp 0', $service->formatCurrency(0));
        $this->assertEquals('Rp 500', $service->formatCurrency(500));
        $this->assertEquals('Rp 1.000', $service->formatCurrency(1000));
    }

    public function test_kas_service_format_currency_angka_besar(): void
    {
        $service = app(KasService::class);
        $this->assertEquals('Rp 1.500.000', $service->formatCurrency(1500000));
        $this->assertEquals('Rp 10.000.000', $service->formatCurrency(10000000));
    }

    public function test_kas_service_total_saldo_formula_benar(): void
    {
        $user = $this->aktifUser();

        $this->buatTagihan($user, '2025-01', 'lunas', 100000, ['tanggal_bayar' => now()]);
        $this->buatTransaksi('masuk',  200000);
        $this->buatTransaksi('keluar', 50000);

        $service = app(KasService::class);

        // (100.000 iuran) + (200.000 masuk) - (50.000 keluar) = 250.000
        $this->assertEquals(250000, $service->totalSaldo());
    }

    public function test_kas_service_total_iuran_lunas_hanya_hitung_yang_lunas(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01', 'lunas',         80000, ['tanggal_bayar' => now()]);
        $this->buatTagihan($user, '2025-02', 'belum_dibayar', 50000);

        $service = app(KasService::class);
        $this->assertEquals(80000, $service->totalIuranLunas());
    }

    public function test_kas_service_total_kas_masuk_hanya_hitung_transaksi_masuk(): void
    {
        $this->buatTransaksi('masuk',  150000);
        $this->buatTransaksi('masuk',  100000);
        $this->buatTransaksi('keluar', 200000);

        $service = app(KasService::class);
        $this->assertEquals(250000, $service->totalKasMasuk());
    }

    public function test_kas_service_total_kas_keluar_hanya_hitung_transaksi_keluar(): void
    {
        $this->buatTransaksi('masuk',  300000);
        $this->buatTransaksi('keluar', 80000);
        $this->buatTransaksi('keluar', 70000);

        $service = app(KasService::class);
        $this->assertEquals(150000, $service->totalKasKeluar());
    }

    // ══════════════════════════════════════════════════════════════
    // TAGIHAN KAS MODEL — helpers & accessors
    // ══════════════════════════════════════════════════════════════

    public function test_mark_as_paid_mengubah_status_ke_lunas(): void
    {
        $user    = $this->aktifUser();
        $tagihan = $this->buatTagihan($user, '2025-01', 'belum_dibayar');

        $this->assertTrue($tagihan->isUnpaid());

        $tagihan->markAsPaid();

        $tagihan->refresh();
        $this->assertTrue($tagihan->isPaid());
        $this->assertNotNull($tagihan->tanggal_bayar);
    }

    public function test_mark_as_paid_mencatat_tanggal_bayar(): void
    {
        $user    = $this->aktifUser();
        $tagihan = $this->buatTagihan($user, '2025-01');

        $tagihan->markAsPaid();

        $this->assertNotNull($tagihan->fresh()->tanggal_bayar);
    }

    public function test_bulan_tagihan_format_semua_bulan_indonesia(): void
    {
        $bulanIndo = [
            '2025-01' => 'Januari 2025',
            '2025-02' => 'Februari 2025',
            '2025-03' => 'Maret 2025',
            '2025-04' => 'April 2025',
            '2025-05' => 'Mei 2025',
            '2025-06' => 'Juni 2025',
            '2025-07' => 'Juli 2025',
            '2025-08' => 'Agustus 2025',
            '2025-09' => 'September 2025',
            '2025-10' => 'Oktober 2025',
            '2025-11' => 'November 2025',
            '2025-12' => 'Desember 2025',
        ];

        foreach ($bulanIndo as $bulan => $expected) {
            $tagihan = TagihanKas::make(['bulan_tagihan' => $bulan]);
            $this->assertEquals($expected, $tagihan->bulan_tagihan_format, "Format salah untuk bulan {$bulan}");
        }
    }

    public function test_nominal_format_menggunakan_separator_titik(): void
    {
        $user    = $this->aktifUser();
        $tagihan = $this->buatTagihan($user, '2025-01', 'belum_dibayar', 1500000);

        $this->assertEquals('Rp 1.500.000', $tagihan->nominal_format);
    }

    public function test_is_paid_dan_is_unpaid_sesuai_status(): void
    {
        $user     = $this->aktifUser();
        $belum    = $this->buatTagihan($user, '2025-01', 'belum_dibayar');
        $sudahBayar = $this->buatTagihan($user, '2025-02', 'lunas', 50000, ['tanggal_bayar' => now()]);

        $this->assertTrue($belum->isUnpaid());
        $this->assertFalse($belum->isPaid());

        $this->assertTrue($sudahBayar->isPaid());
        $this->assertFalse($sudahBayar->isUnpaid());
    }

    public function test_unique_constraint_satu_tagihan_per_user_per_bulan(): void
    {
        $user = $this->aktifUser();
        $this->buatTagihan($user, '2025-01');

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->buatTagihan($user, '2025-01'); // duplikat → exception
    }

    public function test_user_berbeda_boleh_punya_tagihan_bulan_sama(): void
    {
        $userA = $this->aktifUser();
        $userB = $this->aktifUser();

        $this->buatTagihan($userA, '2025-01');
        $this->buatTagihan($userB, '2025-01'); // tidak konflik

        $this->assertDatabaseCount('tagihan_kas', 2);
    }

    // ══════════════════════════════════════════════════════════════
    // INFO USER — GET /api/kas/user
    // ══════════════════════════════════════════════════════════════

    public function test_kas_user_mengembalikan_data_pengguna_yang_login(): void
    {
        $user = $this->aktifUser(['name' => 'Budi Santoso']);

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/user')
            ->assertOk();

        $this->assertEquals('Budi Santoso', $response->json('data.name'));
        $this->assertEquals($user->id, $response->json('data.id'));
    }

    public function test_kas_user_tidak_mengembalikan_data_user_lain(): void
    {
        $userA = $this->aktifUser(['name' => 'User A']);
        $userB = $this->aktifUser(['name' => 'User B']);

        $response = $this->withToken($this->tokenFor($userA))
            ->getJson('/api/kas/user')
            ->assertOk();

        $this->assertEquals('User A', $response->json('data.name'));
        $this->assertNotEquals($userB->id, $response->json('data.id'));
    }

    public function test_kas_user_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/kas/user')->assertUnauthorized();
    }

    public function test_kas_user_diblokir_untuk_demisioner(): void
    {
        $user = $this->demisionerUser();

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/kas/user')
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }
}
