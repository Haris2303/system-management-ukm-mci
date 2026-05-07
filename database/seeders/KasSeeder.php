<?php

namespace Database\Seeders;

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
        // Ambil semua ID user yang ada
        $userIds = User::pluck('id');

        if ($userIds->isEmpty()) {
            $this->command->warn("Tidak ada user ditemukan. Silakan isi tabel users terlebih dahulu.");
            return;
        }

        // --- Seeder untuk Tagihan Kas ---
        $tagihan = [];
        $bulans = ['2025-01', '2025-02', '2025-03'];

        foreach ($userIds as $userId) {
            foreach ($bulans as $bulan) {
                $isLunas = rand(0, 1); // Random status

                $tagihan[] = [
                    'user_id'       => $userId,
                    'bulan_tagihan' => $bulan,
                    'nominal'       => 50000, // Contoh nominal flat 50rb
                    'status'        => $isLunas ? 'lunas' : 'belum_dibayar',
                    'tanggal_bayar' => $isLunas ? Carbon::parse($bulan . '-05') : null,
                    'catatan'       => $isLunas ? 'Dibayar tepat waktu' : null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }
        DB::table('tagihan_kas')->insert($tagihan);

        // --- Seeder untuk Transaksi Kas (Arus Kas) ---
        DB::table('transaksi_kas')->insert([
            [
                'jenis'        => 'masuk',
                'nominal'      => 1500000,
                'keterangan'   => 'Saldo awal kas periode 2025',
                'tanggal'      => '2025-01-01',
                'bukti'        => null,
                'dicatat_oleh' => $userIds->random(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'jenis'        => 'keluar',
                'nominal'      => 200000,
                'keterangan'   => 'Pembelian sapu dan alat kebersihan',
                'tanggal'      => '2025-01-10',
                'bukti'        => 'bukti_nota_01.jpg',
                'dicatat_oleh' => $userIds->random(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'jenis'        => 'masuk',
                'nominal'      => 500000,
                'keterangan'   => 'Sumbangan sukarela donatur',
                'tanggal'      => '2025-02-15',
                'bukti'        => null,
                'dicatat_oleh' => $userIds->random(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        $this->command->info("Seeder Tagihan dan Transaksi Kas berhasil dijalankan.");
    }
}
