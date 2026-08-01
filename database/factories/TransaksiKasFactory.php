<?php

namespace Database\Factories;

use App\Models\TransaksiKas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransaksiKas>
 */
class TransaksiKasFactory extends Factory
{
    protected $model = TransaksiKas::class;

    public function definition(): array
    {
        $jenis = fake()->randomElement(['masuk', 'keluar']);

        $keteranganMasuk = [
            'Sumbangan sukarela donatur', 'Dana hibah kemahasiswaan', 'Hasil penjualan merchandise',
            'Sponsorship acara divisi', 'Kas tambahan dari kegiatan bazaar',
        ];
        $keteranganKeluar = [
            'Pembelian alat tulis kantor', 'Konsumsi rapat pengurus', 'Biaya cetak banner kegiatan',
            'Pembelian alat kebersihan sekretariat', 'Biaya transportasi kegiatan divisi',
            'Sewa tempat workshop', 'Biaya percetakan sertifikat',
        ];

        return [
            'jenis'        => $jenis,
            'nominal'      => $jenis === 'masuk' ? fake()->numberBetween(100_000, 2_000_000) : fake()->numberBetween(50_000, 500_000),
            'keterangan'   => $jenis === 'masuk' ? fake()->randomElement($keteranganMasuk) : fake()->randomElement($keteranganKeluar),
            'tanggal'      => fake()->dateTimeBetween('-7 months', 'now'),
            'bukti'        => null,
            'dicatat_oleh' => User::factory(),
        ];
    }
}
