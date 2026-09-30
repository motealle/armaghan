<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_admin_users_can_access_the_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $inactiveAdmin = User::factory()->admin()->inactive()->create();

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertFalse($customer->canAccessPanel($panel));
        $this->assertFalse($inactiveAdmin->canAccessPanel($panel));
    }
}
