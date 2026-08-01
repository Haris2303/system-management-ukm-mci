<?php

namespace Database\Factories;

use App\Models\Divisi;
use App\Models\Pendaftar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pendaftar>
 */
class PendaftarFactory extends Factory
{
    protected $model = Pendaftar::class;

    public function definition(): array
    {
        return [
            'divisi_id' => Divisi::factory(),
            'nama'      => fake()->name(),
            'nim'       => fake()->unique()->numerify('2#########'),
            'email'     => fake()->unique()->safeEmail(),
            'no_hp'     => '08' . fake()->numerify('##########'),
            'angkatan'  => (string) fake()->numberBetween(2023, 2026),
            'status'    => fake()->randomElement(['menunggu', 'menunggu', 'lulus', 'ditolak']),
            'user_id'   => null,
        ];
    }
}
