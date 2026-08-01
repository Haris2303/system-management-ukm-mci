<?php

namespace Database\Factories;

use App\Models\Pengurusmen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengurusmen>
 */
class PengurusmenFactory extends Factory
{
    protected $model = Pengurusmen::class;

    public function definition(): array
    {
        return [
            'nama'      => fake()->name(),
            'jabatan'   => fake()->randomElement(['Anggota Aktif', 'Staff Divisi', 'Koordinator Acara']),
            'divisi'    => fake()->randomElement(['Programming', 'Desain Grafis', 'Cinematography', 'Game Developer']),
            'foto'      => null,
            'angkatan'  => (string) fake()->numberBetween(2022, 2025),
            'instagram' => '@' . fake()->userName(),
            'linkedin'  => null,
            'urut'      => fake()->numberBetween(1, 30),
            'is_active' => true,
        ];
    }
}
