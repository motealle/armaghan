<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProvisioningCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_be_provisioned_idempotently_from_runtime_environment(): void
    {
        try {
            putenv('ARMAGHAN_ADMIN_NAME=Armaghan Admin');
            putenv('ARMAGHAN_ADMIN_EMAIL=admin@example.test');
            putenv('ARMAGHAN_ADMIN_PASSWORD=example-password-123');

            $this->assertSame(0, Artisan::call('armaghan:admin:ensure'));
            $this->assertStringContainsString('Administrator account ensured.', Artisan::output());

            $admin = User::query()->where('email', 'admin@example.test')->sole();
            $this->assertSame(UserRole::Admin, $admin->role);
            $this->assertTrue($admin->active);
            $this->assertTrue(Hash::check('example-password-123', $admin->password));

            putenv('ARMAGHAN_ADMIN_NAME=Updated Admin');
            putenv('ARMAGHAN_ADMIN_PASSWORD=changed-password-456');

            $this->assertSame(0, Artisan::call('armaghan:admin:ensure'));
            $this->assertSame(1, User::query()->where('email', 'admin@example.test')->count());

            $admin->refresh();
            $this->assertSame('Updated Admin', $admin->name);
            $this->assertTrue(Hash::check('changed-password-456', $admin->password));
        } finally {
            putenv('ARMAGHAN_ADMIN_NAME');
            putenv('ARMAGHAN_ADMIN_EMAIL');
            putenv('ARMAGHAN_ADMIN_PASSWORD');
        }
    }

    public function test_admin_provisioning_rejects_missing_or_weak_runtime_values(): void
    {
        try {
            putenv('ARMAGHAN_ADMIN_NAME=Admin');
            putenv('ARMAGHAN_ADMIN_EMAIL=not-an-email');
            putenv('ARMAGHAN_ADMIN_PASSWORD=short');

            $this->assertSame(1, Artisan::call('armaghan:admin:ensure'));
            $this->assertSame(0, User::query()->count());
        } finally {
            putenv('ARMAGHAN_ADMIN_NAME');
            putenv('ARMAGHAN_ADMIN_EMAIL');
            putenv('ARMAGHAN_ADMIN_PASSWORD');
        }
    }
}
