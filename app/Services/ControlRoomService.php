<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * ADMIN-002 — OneMember Control Room. Internal-only production health snapshot
 * plus external-service dependency detection. Phase 1: no external API calls —
 * external services are marked "manual check required" unless a credential/config
 * is present locally (then "configured", still manual to verify liveness).
 *
 * Status levels: healthy | warning | critical | manual.
 */
class ControlRoomService
{
    public const HEALTHY  = 'healthy';
    public const WARNING  = 'warning';
    public const CRITICAL = 'critical';
    public const MANUAL   = 'manual';

    /** @return list<array{key:string,label:string,status:string,detail:string}> */
    public function internal(): array
    {
        return [
            $this->row('app_status', 'Application', self::HEALTHY, 'Responding'),
            $this->row('version', 'App version', self::HEALTHY, (string) config('app.version', '1.0')),
            $this->row('environment', 'Environment', config('app.env') === 'production' ? self::HEALTHY : self::WARNING, (string) config('app.env')),
            $this->row('debug', 'Debug mode', config('app.debug') ? self::CRITICAL : self::HEALTHY, config('app.debug') ? 'ON — must be OFF in production' : 'Off'),
            $this->database(),
            $this->queue(),
            $this->failedJobs(),
            $this->row('scheduler', 'Scheduler', File::exists(base_path('routes/console.php')) ? self::HEALTHY : self::WARNING,
                File::exists(base_path('routes/console.php')) ? 'Defined (verify cron heartbeat on host)' : 'routes/console.php missing'),
            $this->mail(),
            $this->resendDomain(),
            $this->storageLink(),
            $this->disk(),
            $this->backupPath(),
            $this->lastBackup(),
            $this->row('ssl_domains', 'SSL / domains', self::MANUAL, 'App: ' . config('domains.app') . ' · Corp: ' . config('domains.corporate') . ' — verify certs on host'),
            $this->lastDeploy(),
        ];
    }

    /** @return list<array{key:string,status:string}> feature flags */
    public function featureFlags(): array
    {
        $flags = [];
        foreach (config('features', []) as $key => $value) {
            $flags[] = ['key' => $key, 'status' => $value ? self::HEALTHY : self::WARNING];
        }

        return $flags;
    }

    /** Config warnings worth surfacing regardless of individual rows. */
    public function warnings(): array
    {
        $warnings = [];

        if (config('app.debug')) {
            $warnings[] = 'APP_DEBUG is ON — never in production.';
        }
        if (config('app.env') !== 'production') {
            $warnings[] = 'APP_ENV is "' . config('app.env') . '" — expected "production" in production.';
        }
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            $warnings[] = 'Mail driver is "' . config('mail.default') . '" — real email is not being sent.';
        }
        if (config('queue.default') === 'sync') {
            $warnings[] = 'Queue driver is "sync" — jobs run inline, not in the background.';
        }
        if (blank(config('app.key'))) {
            $warnings[] = 'APP_KEY is not set — run php artisan key:generate.';
        }

        return $warnings;
    }

    /**
     * External service dependency register. Phase 1: detect config presence only;
     * never call external APIs.
     *
     * @return list<array{service:string,purpose:string,status:string,configured:bool,action:string,notes:string}>
     */
    public function external(): array
    {
        return [
            $this->service('DigitalOcean', 'Hosting / infrastructure', false,
                'Manual: check droplet & managed DB health in the DO dashboard.', 'cloud.digitalocean.com'),
            $this->service('Laravel Forge', 'Server provisioning & deploys', false,
                'Manual: check site, deploy log, and daemons in Forge.', 'forge.laravel.com'),
            $this->service('Resend', 'Transactional email', filled(config('services.resend.key')),
                filled(config('services.resend.key')) ? 'Verify sending domain is verified in Resend.' : 'Set RESEND_API_KEY and verify the sending domain.',
                'resend.com/domains'),
            $this->service('GitHub', 'Source control & CI', false,
                'Manual: confirm repo, branch protection, and Actions status.', 'github.com'),
            $this->service('Cloudflare', 'DNS / CDN / WAF', false,
                'Manual: check DNS records, proxy status, and SSL mode.', 'dash.cloudflare.com'),
            $this->service('Stripe', 'Subscription billing', filled(config('stripe.secret_key')),
                (str_starts_with((string) config('stripe.secret_key'), 'sk_test') ? 'TEST keys detected — switch to live before launch.' : 'Verify webhook endpoint & live keys in Stripe.'),
                'dashboard.stripe.com'),
            $this->service('Sentry', 'Error tracking', filled(env('SENTRY_DSN')),
                filled(env('SENTRY_DSN')) ? 'Verify events are arriving in Sentry.' : 'Set SENTRY_DSN to enable error tracking.',
                'sentry.io'),
            $this->service('PostHog', 'Product analytics', filled(env('POSTHOG_API_KEY')),
                filled(env('POSTHOG_API_KEY')) ? 'Verify events are arriving in PostHog.' : 'Set POSTHOG_API_KEY to enable analytics.',
                'posthog.com'),
        ];
    }

    // ── internal check builders ──────────────────────────────────────────

    private function database(): array
    {
        try {
            DB::select('SELECT 1');
            return $this->row('database', 'Database', self::HEALTHY, 'Connected · ' . ucfirst(DB::connection()->getDriverName()));
        } catch (Throwable) {
            return $this->row('database', 'Database', self::CRITICAL, 'Connection failed');
        }
    }

    private function queue(): array
    {
        $driver = config('queue.default', 'sync');
        return $this->row('queue', 'Queue driver', $driver === 'sync' ? self::WARNING : self::HEALTHY,
            $driver . ($driver === 'sync' ? ' — jobs run inline' : ''));
    }

    private function failedJobs(): array
    {
        try {
            $count = DB::table('failed_jobs')->count();
        } catch (Throwable) {
            return $this->row('failed_jobs', 'Failed jobs', self::MANUAL, 'failed_jobs table unavailable');
        }

        return $this->row('failed_jobs', 'Failed jobs',
            $count === 0 ? self::HEALTHY : ($count > 10 ? self::CRITICAL : self::WARNING),
            (string) $count);
    }

    private function mail(): array
    {
        $driver     = config('mail.default', 'log');
        $configured = ! in_array($driver, ['log', 'array'], true);
        return $this->row('mail', 'Mail driver', $configured ? self::HEALTHY : self::WARNING,
            $driver . ($configured ? '' : ' — dev mode, no real email'));
    }

    private function resendDomain(): array
    {
        $hasKey = filled(config('services.resend.key'));
        $from   = config('mail.from.address');
        return $this->row('resend_domain', 'Resend domain', $hasKey ? self::HEALTHY : self::MANUAL,
            $hasKey ? 'Key set · from ' . $from . ' (verify domain in Resend)' : 'No RESEND_API_KEY — manual check');
    }

    private function storageLink(): array
    {
        $linked = File::exists(public_path('storage'));
        return $this->row('storage_link', 'Storage link', $linked ? self::HEALTHY : self::WARNING,
            $linked ? 'Linked' : 'Run php artisan storage:link');
    }

    private function disk(): array
    {
        try {
            $free  = disk_free_space(storage_path());
            $total = disk_total_space(storage_path());
            $usedPct = $total > 0 ? (int) round((($total - $free) / $total) * 100) : 0;
            $status  = $usedPct >= 90 ? self::CRITICAL : ($usedPct >= 75 ? self::WARNING : self::HEALTHY);
            return $this->row('disk', 'Disk usage', $status, $usedPct . '% used · ' . $this->bytes($free) . ' free');
        } catch (Throwable) {
            return $this->row('disk', 'Disk usage', self::MANUAL, 'Unavailable');
        }
    }

    private function backupPath(): array
    {
        $path = env('BACKUP_PATH');
        if (blank($path)) {
            return $this->row('backup_path', 'Backup path', self::WARNING, 'BACKUP_PATH not configured');
        }
        $exists = File::isDirectory($path);
        return $this->row('backup_path', 'Backup path', $exists ? self::HEALTHY : self::WARNING,
            $exists ? $path : $path . ' (not found on this host)');
    }

    private function lastBackup(): array
    {
        $path = env('BACKUP_PATH');
        if (blank($path) || ! File::isDirectory($path)) {
            return $this->row('last_backup', 'Last backup', self::MANUAL, 'No accessible backup directory');
        }

        $files = collect(File::files($path))
            ->filter(fn ($f) => str_ends_with($f->getFilename(), '.sql.gz'))
            ->sortByDesc(fn ($f) => $f->getMTime());

        if ($files->isEmpty()) {
            return $this->row('last_backup', 'Last backup', self::WARNING, 'No .sql.gz backups found');
        }

        $latest = $files->first();
        $age    = now()->diffInHours(\Illuminate\Support\Carbon::createFromTimestamp($latest->getMTime()));
        $status = $age > 26 ? self::WARNING : self::HEALTHY;   // daily backup expected
        return $this->row('last_backup', 'Last backup', $status,
            $latest->getFilename() . ' · ' . round($age) . 'h ago');
    }

    private function lastDeploy(): array
    {
        $head = base_path('.git/HEAD');
        if (! File::exists($head)) {
            return $this->row('last_deploy', 'Last deploy / commit', self::MANUAL, 'No git metadata on host');
        }

        try {
            $ref = trim(File::get($head));
            if (str_starts_with($ref, 'ref: ')) {
                $refPath = base_path('.git/' . substr($ref, 5));
                $commit  = File::exists($refPath) ? substr(trim(File::get($refPath)), 0, 8) : 'unknown';
                $branch  = basename(substr($ref, 5));
            } else {
                $commit = substr($ref, 0, 8);
                $branch = 'detached';
            }
            return $this->row('last_deploy', 'Last deploy / commit', self::HEALTHY, $commit . ' (' . $branch . ')');
        } catch (Throwable) {
            return $this->row('last_deploy', 'Last deploy / commit', self::MANUAL, 'Unreadable git metadata');
        }
    }

    // ── helpers ──────────────────────────────────────────────────────────

    private function row(string $key, string $label, string $status, string $detail): array
    {
        return compact('key', 'label', 'status', 'detail');
    }

    private function service(string $service, string $purpose, bool $configured, string $action, string $notes): array
    {
        return [
            'service'    => $service,
            'purpose'    => $purpose,
            'status'     => $configured ? self::HEALTHY : self::MANUAL,
            'configured' => $configured,
            'action'     => $action,
            'notes'      => $notes,
        ];
    }

    private function bytes(float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
