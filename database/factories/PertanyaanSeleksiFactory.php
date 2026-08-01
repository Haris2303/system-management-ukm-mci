<?php

namespace Database\Factories;

use App\Models\Divisi;
use App\Models\PertanyaanSeleksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PertanyaanSeleksi>
 */
class PertanyaanSeleksiFactory extends Factory
{
    protected $model = PertanyaanSeleksi::class;

    public function definition(): array
    {
        return [
            'divisi_id'       => Divisi::factory(),
            'pertanyaan_teks' => fake()->sentence(10) . '?',
            'is_active'       => true,
            'urut'            => fake()->numberBetween(1, 5),
        ];
    }
}
