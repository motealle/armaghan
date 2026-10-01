<?php

namespace Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDO;
use Tests\TestCase;

class BackupSqliteDatabaseTest extends TestCase
{
    private Filesystem $files;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->root = sys_get_temp_dir().DIRECTORY_SEPARATOR.'armaghan-sqlite-backup-'.Str::uuid();

        $this->files->ensureDirectoryExists($this->root);
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        $this->files->deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_command_creates_a_consistent_sqlite_snapshot(): void
    {
        $source = $this->root.DIRECTORY_SEPARATOR.'source.sqlite';
        $backupDir = $this->root.DIRECTORY_SEPARATOR.'backups';

        $this->files->put($source, '');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $source,
            'armaghan.sqlite.backup_path' => $backupDir,
        ]);

        DB::purge('sqlite');
        DB::connection('sqlite')->statement('CREATE TABLE sample_records (id INTEGER PRIMARY KEY, name TEXT NOT NULL)');
        DB::connection('sqlite')->table('sample_records')->insert([
            ['name' => 'ارمغان'],
            ['name' => 'نمونه'],
        ]);

        $exit = Artisan::call('armaghan:backup-sqlite');

        $this->assertSame(0, $exit);

        $backups = $this->files->files($backupDir);
        $this->assertCount(1, $backups);

        $pdo = new PDO('sqlite:'.$backups[0]);
        $count = $pdo->query('SELECT COUNT(*) FROM sample_records')->fetchColumn();

        $this->assertSame(2, (int) $count);
    }

    public function test_command_refuses_non_sqlite_primary_connection(): void
    {
        config(['database.default' => 'mysql']);

        $exit = Artisan::call('armaghan:backup-sqlite');

        $this->assertSame(1, $exit);
    }
}
