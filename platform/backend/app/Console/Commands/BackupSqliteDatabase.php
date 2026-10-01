<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BackupSqliteDatabase extends Command
{
    protected $signature = 'armaghan:backup-sqlite
        {--path= : Override backup directory for this run}';

    protected $description = 'Create a consistent SQLite snapshot using VACUUM INTO';

    public function handle(Filesystem $files): int
    {
        if (config('database.default') !== 'sqlite') {
            $this->error('SQLite is not the active database connection.');

            return self::FAILURE;
        }

        $database = (string) config('database.connections.sqlite.database');

        if ($database === '' || $database === ':memory:') {
            $this->error('SQLite backup requires a persistent database file.');

            return self::FAILURE;
        }

        $database = $this->absolutePath($database);

        if (! $files->exists($database)) {
            $this->error("SQLite database file does not exist: {$database}");

            return self::FAILURE;
        }

        $directory = (string) ($this->option('path') ?: config('armaghan.sqlite.backup_path'));

        if ($directory === '') {
            throw new RuntimeException('SQLite backup directory is not configured.');
        }

        $directory = $this->absolutePath($directory);
        $files->ensureDirectoryExists($directory);

        $name = sprintf(
            'armaghan-%s-%s.sqlite',
            now()->format('Ymd-His'),
            bin2hex(random_bytes(3)),
        );

        $destination = $directory.DIRECTORY_SEPARATOR.$name;

        if ($files->exists($destination)) {
            throw new RuntimeException("Backup destination already exists: {$destination}");
        }

        $pdo = DB::connection('sqlite')->getPdo();
        $quotedDestination = $pdo->quote($destination);

        if (! is_string($quotedDestination)) {
            throw new RuntimeException('Unable to quote SQLite backup destination.');
        }

        DB::connection('sqlite')->statement("VACUUM INTO {$quotedDestination}");

        clearstatcache(true, $destination);

        if (! $files->exists($destination) || $files->size($destination) <= 0) {
            throw new RuntimeException('SQLite backup did not produce a valid file.');
        }

        $this->info($destination);

        return self::SUCCESS;
    }

    private function absolutePath(string $path): string
    {
        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1) {
            return $path;
        }

        return base_path($path);
    }
}
