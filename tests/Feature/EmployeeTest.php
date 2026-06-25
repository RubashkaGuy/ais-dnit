<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Qualification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_belongs_to_position_and_department(): void
    {
        $position = Position::factory()->create();
        $department = Department::factory()->create();

        $employee = Employee::factory()->create([
            'position_id' => $position->id,
            'department_id' => $department->id,
        ]);

        $this->assertTrue($employee->position->is($position));
        $this->assertTrue($employee->department->is($department));
    }

    public function test_employee_has_many_qualifications(): void
    {
        $employee = Employee::factory()->create();
        Qualification::factory()->count(3)->create(['employee_id' => $employee->id]);

        $this->assertCount(3, $employee->refresh()->qualifications);
    }

    public function test_employee_can_be_created_with_all_fields(): void
    {
        $position = Position::factory()->create();
        $department = Department::factory()->create();

        $employee = Employee::create([
            'full_name' => 'Иванов Иван Иванович',
            'position_id' => $position->id,
            'department_id' => $department->id,
            'hire_date' => '2024-01-15',
            'education' => 'Высшее техническое',
            'phone' => '+7 (937) 123-45-67',
        ]);

        $this->assertDatabaseHas('employees', [
            'full_name' => 'Иванов Иван Иванович',
            'phone' => '+7 (937) 123-45-67',
        ]);
        $this->assertEquals('2024-01-15', $employee->hire_date->format('Y-m-d'));
    }
}
