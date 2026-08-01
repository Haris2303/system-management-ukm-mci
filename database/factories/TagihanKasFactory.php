<?php

namespace Database\Factories;

use App\Models\TagihanKas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TagihanKas>
 */
class TagihanKasFactory extends Factory
{
    protected $model = TagihanKas::class;

    public function definition(): array
    {
        $isLunas = fake()->boolean(70);
        $bulan = fake()->randomElement([
            '2026-01', '2026-02', '2026-03', '2026-04', '2026-05', '2026-06', '2026-07',
        ]);

        return [
            'user_id'       => User::factory(),
            'bulan_tagihan' => $bulan,
            'nominal'       => 25000,
            'status'        => $isLunas ? 'lunas' : 'belum_dibayar',
            'tanggal_bayar' => $isLunas ? fake()->dateTimeBetween($bulan . '-01', $bulan . '-28') : null,
            'catatan'       => $isLunas ? fake()->randomElement(['Dibayar tunai', 'Transfer via bendahara', 'Dibayar tepat waktu', null]) : null,
        ];
    }

    public function lunas(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'        => 'lunas',
            'tanggal_bayar' => fake()->dateTimeBetween($attributes['bulan_tagihan'] . '-01', $attributes['bulan_tagihan'] . '-28'),
        ]);
    }

    public function belumDibayar(): static
    {
        return $this->state(fn () => [
            'status'        => 'belum_dibayar',
            'tanggal_bayar' => null,
            'catatan'       => null,
        ]);
    }
}
