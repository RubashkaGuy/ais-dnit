<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_delete_employee_but_admin_can(): void
    {
        $staff = User::factory()->staff()->create();
        $admin = User::factory()->admin()->create();
        $employee = Employee::factory()->create();

        $this->assertTrue($staff->can('view', $employee));
        $this->assertTrue($staff->can('update', $employee));
        $this->assertFalse($staff->can('delete', $employee));

        $this->assertTrue($admin->can('delete', $employee));
    }

    public function test_staff_can_view_but_not_modify_directory_records(): void
    {
        $staff = User::factory()->staff()->create();
        $admin = User::factory()->admin()->create();
        $department = Department::factory()->create();

        $this->assertTrue($staff->can('view', $department));
        $this->assertFalse($staff->can('update', $department));
        $this->assertFalse($staff->can('delete', $department));

        $this->assertTrue($admin->can('update', $department));
    }

    public function test_only_admin_can_manage_users(): void
    {
        $staff = User::factory()->staff()->create();
        $admin = User::factory()->admin()->create();
        $target = User::factory()->staff()->create();

        $this->assertFalse($staff->can('viewAny', User::class));
        $this->assertFalse($staff->can('update', $target));

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertTrue($admin->can('update', $target));
        $this->assertFalse($admin->can('delete', $admin));
    }
}
