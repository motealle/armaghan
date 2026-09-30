<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class EnsureAdminUser extends Command
{
    protected $signature = 'armaghan:admin:ensure';

    protected $description = 'Create or update the Armaghan administrator from runtime environment values';

    public function handle(): int
    {
        $name = trim((string) getenv('ARMAGHAN_ADMIN_NAME'));
        $email = trim((string) getenv('ARMAGHAN_ADMIN_EMAIL'));
        $password = (string) getenv('ARMAGHAN_ADMIN_PASSWORD');

        if ($name === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Valid administrator name and email environment values are required.');

            return self::FAILURE;
        }

        if (mb_strlen($password) < 14) {
            $this->error('Administrator password must contain at least 14 characters.');

            return self::FAILURE;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => UserRole::Admin,
                'active' => true,
            ],
        );

        $this->info('Administrator account ensured.');

        return self::SUCCESS;
    }
}
