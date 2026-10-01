<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProvisionFirstAdmin extends Command
{
    protected $signature = 'armaghan:provision-first-admin
        {--name=Armaghan Administrator : Administrator display name}
        {--email=admin@armaghan.local : Administrator login email}
        {--password-env=ARMAGHAN_BOOTSTRAP_ADMIN_PASSWORD : Environment variable containing the one-time password}';

    protected $description = 'Provision the first active administrator without repository-stored credentials';

    public function handle(): int
    {
        if (User::query()->where('role', UserRole::Admin->value)->where('active', true)->exists()) {
            $this->error('An active administrator already exists. Refusing first-admin provisioning.');

            return self::FAILURE;
        }

        $email = strtolower(trim((string) $this->option('email')));
        $name = trim((string) $this->option('name'));
        $passwordEnv = trim((string) $this->option('password-env'));
        $password = $passwordEnv === '' ? false : getenv($passwordEnv);

        if ($name === '' || strlen($name) > 120) {
            $this->error('Administrator name is invalid.');

            return self::FAILURE;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            $this->error('Administrator email is invalid.');

            return self::FAILURE;
        }

        if (! is_string($password) || strlen($password) < 20) {
            $this->error('A strong one-time password must be supplied through the configured environment variable.');

            return self::FAILURE;
        }

        if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            $this->error('A user with the requested email already exists.');

            return self::FAILURE;
        }

        $admin = DB::transaction(function () use ($name, $email, $password): User {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => UserRole::Admin->value,
                'active' => true,
            ]);

            $user->forceFill([
                'email_verified_at' => now(),
                'remember_token' => Str::random(60),
            ])->save();

            return $user;
        }, 3);

        $this->info(sprintf(
            'First administrator provisioned: user_id=%d email=%s',
            $admin->id,
            $admin->email,
        ));

        return self::SUCCESS;
    }
}
