<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProvisionFirstAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('ARMAGHAN_TEST_ADMIN_PASSWORD');

        parent::tearDown();
    }

    public function test_it_provisions_first_admin_from_environment_password_without_printing_secret(): void
    {
        $password = 'Correct-Horse-Battery-Staple-2026!';
        putenv('ARMAGHAN_TEST_ADMIN_PASSWORD='.$password);

        $exit = Artisan::call('armaghan:provision-first-admin', [
            '--name' => 'Production Administrator',
            '--email' => 'ADMIN@EXAMPLE.INVALID',
            '--password-env' => 'ARMAGHAN_TEST_ADMIN_PASSWORD',
        ]);

        $this->assertSame(0, $exit);

        $admin = User::query()->sole();

        $this->assertSame('admin@example.invalid', $admin->email);
        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->active);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check($password, $admin->password));
        $this->assertStringNotContainsString($password, Artisan::output());
    }

    public function test_it_refuses_to_run_when_an_active_admin_already_exists(): void
    {
        User::factory()->admin()->create();

        putenv('ARMAGHAN_TEST_ADMIN_PASSWORD=Another-Strong-Password-2026!');

        $exit = Artisan::call('armaghan:provision-first-admin', [
            '--email' => 'second@example.invalid',
            '--password-env' => 'ARMAGHAN_TEST_ADMIN_PASSWORD',
        ]);

        $this->assertSame(1, $exit);
        $this->assertSame(1, User::query()->where('role', UserRole::Admin->value)->count());
    }

    public function test_it_refuses_missing_or_short_passwords(): void
    {
        $missing = Artisan::call('armaghan:provision-first-admin', [
            '--email' => 'admin@example.invalid',
            '--password-env' => 'ARMAGHAN_TEST_ADMIN_PASSWORD',
        ]);
        $this->assertSame(1, $missing);
        $this->assertDatabaseCount('users', 0);

        putenv('ARMAGHAN_TEST_ADMIN_PASSWORD=short');

        $short = Artisan::call('armaghan:provision-first-admin', [
            '--email' => 'admin@example.invalid',
            '--password-env' => 'ARMAGHAN_TEST_ADMIN_PASSWORD',
        ]);
        $this->assertSame(1, $short);
        $this->assertDatabaseCount('users', 0);
    }
}
