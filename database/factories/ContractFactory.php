<?php

namespace Database\Factories;

use App\Enums\ContractStatus;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'course_id' => Course::factory(),
            'number' => 'ДНиТ/'.fake()->unique()->numerify('2026-####'),
            'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'amount' => fake()->numberBetween(2000, 20000),
            'status' => fake()->randomElement(ContractStatus::cases()),
        ];
    }
}
