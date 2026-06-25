<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Qualification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualificationCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_next_qualification_date_returns_stored_next_date(): void
    {
        $employee = Employee::factory()->create();

        Qualification::factory()->create([
            'employee_id' => $employee->id,
            'date' => '2024-03-15',
            'next_date' => '2027-03-15',
        ]);

        $this->assertEquals('2027-03-15', $employee->fresh()->next_qualification_date->format('Y-m-d'));
    }

    public function test_next_qualification_date_picks_latest_record(): void
    {
        $employee = Employee::factory()->create();

        Qualification::factory()->create([
            'employee_id' => $employee->id,
            'date' => '2021-05-01',
            'next_date' => '2024-05-01',
        ]);
        Qualification::factory()->create([
            'employee_id' => $employee->id,
            'date' => '2024-09-10',
            'next_date' => '2027-09-10',
        ]);

        $this->assertEquals('2027-09-10', $employee->fresh()->next_qualification_date->format('Y-m-d'));
    }

    public function test_next_qualification_date_is_null_when_no_qualifications(): void
    {
        $employee = Employee::factory()->create();

        $this->assertNull($employee->next_qualification_date);
    }
}
