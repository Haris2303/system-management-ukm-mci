<?php

namespace Database\Factories;

use App\Models\Agenda;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Agenda>
 */
class AgendaFactory extends Factory
{
    protected $model = Agenda::class;

    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('-2 months', '+2 weeks');
        $selesai = (clone $mulai)->modify('+' . fake()->numberBetween(1, 3) . ' hours');

        return [
            'nama_agenda'   => fake()->randomElement([
                'Rapat Rutin Bulanan', 'Rapat Koordinasi Divisi', 'Kumpul Anggota Aktif',
                'Briefing Program Kerja', 'Evaluasi Kegiatan',
            ]),
            'deskripsi'     => fake()->sentence(12),
            'waktu_mulai'   => $mulai,
            'waktu_selesai' => $selesai,
            'lokasi'        => fake()->randomElement(['Sekretariat UKM MCI', 'Ruang Seminar Gedung B', 'Aula Kampus', 'Online via Zoom']),
            'is_active'     => $mulai > now(),
            'qr_code_token' => Str::random(32),
        ];
    }

    public function selesai(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
