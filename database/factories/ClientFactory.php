<?php

namespace Database\Factories;

use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isIndividual = fake()->boolean();

        return [
            'type' => $isIndividual ? ClientType::Individual : ClientType::Company,
            'full_name' => $isIndividual ? fake()->name() : null,
            'org_name' => $isIndividual ? null : fake()->company(),
            'inn' => fake()->numerify(str_repeat('#', 10)),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
        ];
    }

    public function individual(): static
    {
        return $this->state(fn () => [
            'type' => ClientType::Individual,
            'full_name' => fake()->name(),
            'org_name' => null,
        ]);
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type' => ClientType::Company,
            'full_name' => null,
            'org_name' => fake()->company(),
        ]);
    }
}
