<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'position_id' => Position::factory(),
            'department_id' => Department::factory(),
            'hire_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'education' => fake()->randomElement(['Высшее', 'Среднее профессиональное', 'Высшее техническое']),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
