<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Election>
 */
class ElectionFactory extends Factory
{
    protected $model = Election::class;

    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('-1 month', '+1 week');
        $selesai = (clone $mulai)->modify('+3 days');

        return [
            'judul'           => 'Pemilihan Ketua UKM MCI Periode ' . fake()->numberBetween(2026, 2027) . '/' . fake()->numberBetween(2027, 2028),
            'deskripsi'       => 'Pemilihan ketua umum baru UKM MCI melalui mekanisme e-voting anggota aktif.',
            'posisi'          => 'Ketua UKM',
            'waktu_mulai'     => $mulai,
            'waktu_selesai'   => $selesai,
            'status'          => 'draft',
            'is_anonim'       => true,
            'tampil_realtime' => false,
            'created_by'      => User::factory(),
        ];
    }
}
