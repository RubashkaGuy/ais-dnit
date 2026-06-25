<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Курс '.fake()->unique()->sentence(2),
            'hours' => fake()->randomElement([16, 24, 40, 72, 144, 256]),
            'price' => fake()->numberBetween(2000, 20000),
        ];
    }
}
