<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_admin(): void
    {
        $this->get('/')->assertRedirect('/admin');
    }

    public function test_health_endpoint_returns_ok(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_admin_panel_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_login_page_includes_account_help_section(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Как получить аккаунт?');
    }

    public function test_admin_user_can_access_panel(): void
    {
        $admin = User::factory()->admin()->create();
        $panel = Filament::getPanel('admin');

        $this->assertTrue($admin->canAccessPanel($panel));
    }

    public function test_staff_user_can_access_panel(): void
    {
        $staff = User::factory()->staff()->create();
        $panel = Filament::getPanel('admin');

        $this->assertTrue($staff->canAccessPanel($panel));
    }
}
