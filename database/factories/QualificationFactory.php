<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Qualification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Qualification>
 */
class QualificationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = Carbon::parse(fake()->dateTimeBetween('-5 years', '-1 day'));

        return [
            'employee_id' => Employee::factory(),
            'course_name' => fake()->sentence(3),
            'date' => $date->toDateString(),
            'next_date' => $date->copy()->addYears(3)->toDateString(),
        ];
    }
}
