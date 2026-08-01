<?php

namespace Database\Factories;

use App\Models\Materi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Materi>
 */
class MateriFactory extends Factory
{
    protected $model = Materi::class;

    public function definition(): array
    {
        return [
            'judul'       => fake()->sentence(4),
            'deskripsi'   => fake()->sentence(12),
            'file_path'   => null,
            'link_url'    => fake()->boolean(60) ? fake()->url() : null,
            'divisi_id'   => null,
            'uploaded_by' => null,
        ];
    }
}
