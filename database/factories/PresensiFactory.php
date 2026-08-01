<?php

namespace Database\Factories;

use App\Models\Agenda;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presensi>
 */
class PresensiFactory extends Factory
{
    protected $model = Presensi::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'agenda_id' => Agenda::factory(),
            'jam_hadir' => fake()->dateTimeBetween('-2 months', 'now'),
            'status'    => fake()->randomElement(['Hadir', 'Hadir', 'Hadir', 'Izin', 'Absen']),
        ];
    }
}
