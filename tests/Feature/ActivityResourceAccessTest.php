<?php

namespace Tests\Feature;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_activity_log(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $this->assertTrue(ActivityResource::canAccess());
        $this->assertTrue(ActivityResource::canViewAny());
    }

    public function test_staff_cannot_access_activity_log(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff);

        $this->assertFalse(ActivityResource::canAccess());
        $this->assertFalse(ActivityResource::canViewAny());
    }
}
