<?php

use App\Console\Commands\ProcessBirthdayRewards;
use App\Console\Commands\ProcessExpiredTrials;
use App\Console\Commands\ProcessPointExpiry;
use App\Console\Commands\VerifyDatabaseBackup;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run once daily at 01:00 server time (low-traffic window).
// Production: ensure `* * * * * php /path/to/artisan schedule:run` is in crontab.
Schedule::command(ProcessExpiredTrials::class)->dailyAt('01:00');

// Expire points for members inactive past the campaign's expiry window.
Schedule::command(ProcessPointExpiry::class)->dailyAt('02:00');

// Award birthday bonus points to members within their birthday window.
Schedule::command(ProcessBirthdayRewards::class)->dailyAt('08:00');

// Verify that yesterday's database backup exists. Runs at 03:00, two hours after
// the backup cron job (02:00 recommended). Logs pass/fail to storage/logs/laravel.log.
// Set BACKUP_PATH in .env to match your mysqldump output directory.
Schedule::command(VerifyDatabaseBackup::class)->dailyAt('03:00');
