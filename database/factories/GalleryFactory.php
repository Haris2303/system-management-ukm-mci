<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'judul'       => fake()->sentence(4),
            'foto'        => 'gallery/demo-' . fake()->numberBetween(1, 12) . '.jpg',
            'kategori'    => fake()->randomElement(['Kegiatan', 'Workshop', 'Prestasi', 'Rapat', 'Umum']),
            'deskripsi'   => fake()->sentence(10),
            'is_featured' => fake()->boolean(20),
            'urut'        => fake()->numberBetween(1, 20),
        ];
    }
}
