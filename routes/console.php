<?php

use App\Console\Commands\ProcessExpiredTrials;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run once daily at 01:00 server time (low-traffic window).
// Production: ensure `* * * * * php /path/to/artisan schedule:run` is in crontab.
Schedule::command(ProcessExpiredTrials::class)->dailyAt('01:00');
