<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerifyDatabaseBackup extends Command
{
    protected $signature = 'backup:verify {--path= : Directory containing backup files}';

    protected $description = 'Verify that a recent database backup exists';

    public function handle(): int
    {
        $path = $this->option('path') ?? env('BACKUP_PATH', '/var/backups/onemember');

        if (! is_dir($path)) {
            Log::error('Backup verification failed: directory not found.', ['path' => $path]);
            $this->error("Backup directory not found: {$path}");
            return Command::FAILURE;
        }

        $files   = glob("{$path}/db_*.sql.gz") ?: [];
        $cutoff  = now()->subHours(25)->getTimestamp();
        $recent  = array_filter($files, fn ($f) => filemtime($f) >= $cutoff);

        if (empty($recent)) {
            Log::error('Backup verification failed: no recent backup found.', [
                'path'   => $path,
                'cutoff' => now()->subHours(25)->toDateTimeString(),
            ]);
            $this->error("No recent database backup found in: {$path}");
            return Command::FAILURE;
        }

        usort($recent, fn ($a, $b) => filemtime($b) - filemtime($a));
        $latest   = reset($recent);
        $sizeMb   = round(filesize($latest) / 1024 / 1024, 2);
        $ageHours = round((time() - filemtime($latest)) / 3600, 1);

        Log::info('Backup verification passed.', [
            'file'      => basename($latest),
            'size_mb'   => $sizeMb,
            'age_hours' => $ageHours,
        ]);

        $this->info('Backup verified: ' . basename($latest) . " ({$sizeMb} MB, {$ageHours}h old)");
        return Command::SUCCESS;
    }
}
