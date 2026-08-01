<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'nim'          => fake()->unique()->numerify('2#########'),
            'email'        => fake()->unique()->safeEmail(),
            'no_hp'        => '08' . fake()->numerify('##########'),
            'jurusan'      => fake()->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Ilmu Komputer', 'Teknik Elektro', 'Desain Komunikasi Visual']),
            'angkatan'     => (string) fake()->numberBetween(2022, 2025),
            'motivasi'     => fake()->paragraph(3),
            'status'       => fake()->randomElement(['pending', 'pending', 'diterima', 'ditolak']),
            'foto'         => null,
        ];
    }
}
