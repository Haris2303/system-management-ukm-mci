<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'user_id'     => User::factory(),
            'visi'        => fake()->sentence(10),
            'misi'        => implode("\n", fake()->sentences(4)),
            'foto'        => null,
        ];
    }
}
