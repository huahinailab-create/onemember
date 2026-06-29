<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackupVerifyCommandTest extends TestCase
{
    private function tempDir(string $suffix = ''): string
    {
        $dir = sys_get_temp_dir() . '/onemember-backup-test' . $suffix . '-' . uniqid();
        mkdir($dir);
        return $dir;
    }

    private function rmTempDir(string $dir): void
    {
        foreach (glob("{$dir}/*") as $file) {
            unlink($file);
        }
        rmdir($dir);
    }

    public function test_passes_when_recent_backup_exists(): void
    {
        $dir  = $this->tempDir();
        $file = $dir . '/db_' . date('Ymd_His') . '.sql.gz';
        touch($file);

        $this->artisan('backup:verify', ['--path' => $dir])
            ->assertExitCode(0);

        $this->rmTempDir($dir);
    }

    public function test_fails_when_backup_directory_does_not_exist(): void
    {
        $this->artisan('backup:verify', ['--path' => '/nonexistent/path/onemember-xyz'])
            ->assertExitCode(1);
    }

    public function test_fails_when_no_backup_files_exist(): void
    {
        $dir = $this->tempDir('-empty');

        $this->artisan('backup:verify', ['--path' => $dir])
            ->assertExitCode(1);

        $this->rmTempDir($dir);
    }

    public function test_fails_when_backup_is_older_than_25_hours(): void
    {
        $dir  = $this->tempDir('-stale');
        $file = $dir . '/db_20230101_010000.sql.gz';
        touch($file, strtotime('-26 hours'));

        $this->artisan('backup:verify', ['--path' => $dir])
            ->assertExitCode(1);

        $this->rmTempDir($dir);
    }

    public function test_passes_with_multiple_files_and_picks_most_recent(): void
    {
        $dir   = $this->tempDir('-multi');
        $old   = $dir . '/db_20230101_010000.sql.gz';
        $fresh = $dir . '/db_' . date('Ymd_His') . '.sql.gz';

        touch($old, strtotime('-26 hours'));
        touch($fresh);

        $this->artisan('backup:verify', ['--path' => $dir])
            ->assertExitCode(0);

        $this->rmTempDir($dir);
    }
}
