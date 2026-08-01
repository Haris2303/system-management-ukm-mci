<?php

namespace Database\Seeders;

use App\Models\TransaksiKas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kas hanya berlaku untuk pengurus & anggota aktif (bukan demisioner/super_admin)
        $userIds = User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'demisioner']))
            ->pluck('id');

        if ($userIds->isEmpty()) {
            $this->command->warn('Tidak ada user ditemukan. Silakan jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $bendahara = User::role('bendahara')->first()?->id ?? $userIds->random();

        // ── Tagihan Kas: 6 bulan terakhir untuk setiap anggota ──
        $bulans = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->format('Y-m'));

        $tagihan = [];
        foreach ($userIds as $userId) {
            foreach ($bulans as $bulan) {
                // Bulan berjalan & bulan depan sengaja belum lunas semua (demo tunggakan realistis)
                $isLunas = $bulan < Carbon::now()->format('Y-m') ? fake()->boolean(75) : fake()->boolean(30);

                $tagihan[] = [
                    'user_id'       => $userId,
                    'bulan_tagihan' => $bulan,
                    'nominal'       => 25000,
                    'status'        => $isLunas ? 'lunas' : 'belum_dibayar',
                    'tanggal_bayar' => $isLunas ? Carbon::parse($bulan . '-' . fake()->numberBetween(1, 25)) : null,
                    'catatan'       => $isLunas ? fake()->randomElement(['Dibayar tepat waktu', 'Transfer via bendahara', 'Dibayar tunai']) : null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        // Hindari duplikasi kalau seeder dijalankan ulang
        DB::table('tagihan_kas')->upsert(
            $tagihan,
            ['user_id', 'bulan_tagihan'],
            ['nominal', 'status', 'tanggal_bayar', 'catatan', 'updated_at']
        );

        // ── Transaksi Kas (Arus Kas Umum) ──
        if (TransaksiKas::count() === 0) {
            DB::table('transaksi_kas')->insert([
                [
                    'jenis'        => 'masuk',
                    'nominal'      => 1500000,
                    'keterangan'   => 'Saldo awal kas periode 2025/2026',
                    'tanggal'      => Carbon::now()->subMonths(6)->startOfMonth(),
                    'bukti'        => null,
                    'dicatat_oleh' => $bendahara,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'jenis'        => 'keluar',
                    'nominal'      => 200000,
                    'keterangan'   => 'Pembelian alat kebersihan sekretariat',
                    'tanggal'      => Carbon::now()->subMonths(5),
                    'bukti'        => null,
                    'dicatat_oleh' => $bendahara,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'jenis'        => 'masuk',
                    'nominal'      => 500000,
                    'keterangan'   => 'Sumbangan sukarela donatur',
                    'tanggal'      => Carbon::now()->subMonths(4),
                    'bukti'        => null,
                    'dicatat_oleh' => $bendahara,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'jenis'        => 'keluar',
                    'nominal'      => 750000,
                    'keterangan'   => 'Konsumsi & sewa tempat Workshop Laravel',
                    'tanggal'      => Carbon::now()->subMonths(3),
                    'bukti'        => null,
                    'dicatat_oleh' => $bendahara,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'jenis'        => 'masuk',
                    'nominal'      => 350000,
                    'keterangan'   => 'Hasil penjualan merchandise UKM',
                    'tanggal'      => Carbon::now()->subMonths(2),
                    'bukti'        => null,
                    'dicatat_oleh' => $bendahara,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'jenis'        => 'keluar',
                    'nominal'      => 400000,
                    'keterangan'   => 'Cetak banner & sertifikat kegiatan',
                    'tanggal'      => Carbon::now()->subMonth(),
                    'bukti'        => null,
                    'dicatat_oleh' => $bendahara,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
            ]);
        }

        $this->command->info('✅ Seeder Tagihan dan Transaksi Kas berhasil dijalankan.');
    }
}
