<?php

namespace App\Services\DevTools;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class DevSystemService
{
    public function runArtisan(string $command, array $args = []): string
    {
        Artisan::call($command, $args);
        return Artisan::output();
    }

    public function getEnvironmentInfo(): array
    {
        return [
            'laravel_version'   => app()->version(),
            'php_version'       => PHP_VERSION,
            'app_env'           => config('app.env'),
            'app_debug'         => config('app.debug') ? 'true' : 'false',
            'mail_mailer'       => config('mail.default'),
            'queue_connection'  => config('queue.default'),
            'cache_store'       => config('cache.default'),
            'db_connection'     => config('database.default'),
            'filesystem'        => config('filesystems.default'),
            'git_commit'        => $this->getGitCommit(),
            'git_branch'        => $this->getGitBranch(),
            'deployed_at'       => $this->getDeployedAt(),
        ];
    }

    public function getSystemHealth(): array
    {
        return [
            'database'    => $this->checkDatabase(),
            'redis'       => $this->checkRedis(),
            'mail'        => $this->checkMail(),
            'queue'       => $this->checkQueue(),
            'storage'     => $this->checkStorage(),
            'scheduler'   => $this->checkScheduler(),
            'failed_jobs' => $this->getFailedJobsCount(),
            'disk_usage'  => $this->getDiskUsage(),
            'memory'      => $this->getMemoryUsage(),
        ];
    }

    public function getQueueStats(): array
    {
        $failed  = DB::table('failed_jobs')->count();
        $pending = DB::table('jobs')->count();

        return [
            'failed'  => $failed,
            'pending' => $pending,
            'total'   => $failed + $pending,
        ];
    }

    public function sendTestMail(string $to, string $subject, string $body): void
    {
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    public function testResendApi(): array
    {
        try {
            $key = config('services.resend.key');
            if (! $key) {
                return ['ok' => false, 'message' => 'RESEND_API_KEY not set'];
            }
            $client = \Resend::client($key);
            return ['ok' => true, 'message' => 'Resend client initialised successfully'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function clearLogs(): void
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }
    }

    public function clearSessions(): void
    {
        Artisan::call('session:clear');
    }

    public function storageLink(): string
    {
        Artisan::call('storage:link');
        return Artisan::output();
    }

    public function generateFakeMembers(int $merchantId, int $count): void
    {
        $merchant = \App\Models\Merchant::findOrFail($merchantId);
        \App\Models\Member::factory()->count($count)->create([
            'merchant_id' => $merchant->id,
        ]);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'green', 'message' => 'Connected'];
        } catch (\Throwable $e) {
            return ['status' => 'red', 'message' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        try {
            $driver = config('cache.default');
            if ($driver !== 'redis') {
                return ['status' => 'yellow', 'message' => "Cache driver: {$driver}"];
            }
            Cache::store('redis')->put('dev_health_check', 1, 5);
            return ['status' => 'green', 'message' => 'Connected'];
        } catch (\Throwable $e) {
            return ['status' => 'red', 'message' => $e->getMessage()];
        }
    }

    private function checkMail(): array
    {
        $mailer = config('mail.default');
        return ['status' => 'green', 'message' => "Transport: {$mailer}"];
    }

    private function checkQueue(): array
    {
        try {
            $count = DB::table('jobs')->count();
            return ['status' => 'green', 'message' => "{$count} pending jobs"];
        } catch (\Throwable $e) {
            return ['status' => 'red', 'message' => $e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        try {
            Storage::put('dev_health_check.txt', 'ok');
            Storage::delete('dev_health_check.txt');
            return ['status' => 'green', 'message' => 'Writable'];
        } catch (\Throwable $e) {
            return ['status' => 'red', 'message' => $e->getMessage()];
        }
    }

    private function checkScheduler(): array
    {
        return ['status' => 'yellow', 'message' => 'Cannot detect scheduler status at runtime'];
    }

    private function getFailedJobsCount(): array
    {
        try {
            $count = DB::table('failed_jobs')->count();
            $status = $count === 0 ? 'green' : ($count < 10 ? 'yellow' : 'red');
            return ['status' => $status, 'message' => "{$count} failed jobs"];
        } catch (\Throwable $e) {
            return ['status' => 'yellow', 'message' => 'Table not found'];
        }
    }

    private function getDiskUsage(): array
    {
        $free  = disk_free_space('/');
        $total = disk_total_space('/');
        $used  = $total - $free;
        $pct   = round($used / $total * 100);
        $status = $pct < 70 ? 'green' : ($pct < 85 ? 'yellow' : 'red');
        return ['status' => $status, 'message' => "{$pct}% used (" . $this->formatBytes($used) . " / " . $this->formatBytes($total) . ")"];
    }

    private function getMemoryUsage(): array
    {
        $used  = memory_get_usage(true);
        $limit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $pct   = $limit > 0 ? round($used / $limit * 100) : 0;
        $status = $pct < 70 ? 'green' : ($pct < 85 ? 'yellow' : 'red');
        return ['status' => $status, 'message' => $this->formatBytes($used) . " / " . ini_get('memory_limit')];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1024, 1) . ' KB';
    }

    private function parseMemoryLimit(string $val): int
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $num = (int) $val;
        return match($last) {
            'g' => $num * 1073741824,
            'm' => $num * 1048576,
            'k' => $num * 1024,
            default => $num,
        };
    }

    private function getGitCommit(): string
    {
        return trim(shell_exec('git -C ' . base_path() . ' rev-parse --short HEAD 2>/dev/null') ?? 'unknown');
    }

    private function getGitBranch(): string
    {
        return trim(shell_exec('git -C ' . base_path() . ' rev-parse --abbrev-ref HEAD 2>/dev/null') ?? 'unknown');
    }

    private function getDeployedAt(): string
    {
        $file = base_path('DEPLOYED_AT');
        return file_exists($file) ? trim(file_get_contents($file)) : 'unknown';
    }
}
