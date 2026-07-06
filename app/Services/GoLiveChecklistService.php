<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\File;

/**
 * OPS-001 — private-beta / go-live readiness checks. Pure inspection of
 * config and local state; no external services (Operations Manual §16, §20).
 */
class GoLiveChecklistService
{
    /**
     * @return list<array{key:string,pass:bool,detail:string,critical:bool}>
     */
    public function checks(): array
    {
        return [
            $this->check('app_key', filled(config('app.key')), config('app.key') ? 'Set' : 'Missing — run key:generate', true),
            $this->check('debug_off', config('app.debug') === false, config('app.debug') ? 'APP_DEBUG is ON' : 'Off', true),
            $this->check('env_production', config('app.env') === 'production', 'APP_ENV=' . config('app.env'), false),
            $this->check('mail_configured', ! in_array(config('mail.default'), ['log', 'array'], true), 'Driver: ' . config('mail.default'), false),
            $this->check('queue_async', config('queue.default') !== 'sync', 'Driver: ' . config('queue.default'), false),
            $this->check('storage_linked', File::exists(public_path('storage')), File::exists(public_path('storage')) ? 'Linked' : 'Run storage:link', true),
            $this->check('backup_path', filled(env('BACKUP_PATH')), env('BACKUP_PATH') ? 'Configured' : 'BACKUP_PATH not set', false),
            $this->check('terms_version', filled(config('countries.terms_version')), (string) config('countries.terms_version'), true),
            $this->check('admin_exists', User::where('is_admin', true)->exists(), User::where('is_admin', true)->count() . ' admin user(s)', true),
            $this->check('plans_exist', ! empty(config('subscriptions.plans')), count(config('subscriptions.plans', [])) . ' plan(s)', true),
            $this->check('scheduler_documented', File::exists(base_path('routes/console.php')), 'routes/console.php present', false),
            $this->check('feature_flags', is_array(config('features')), 'features: ' . implode(', ', array_keys(config('features', []))), false),
        ];
    }

    public function summary(): array
    {
        $checks   = $this->checks();
        $passed   = count(array_filter($checks, fn ($c) => $c['pass']));
        $critical = array_filter($checks, fn ($c) => $c['critical'] && ! $c['pass']);

        return [
            'checks'          => $checks,
            'passed'          => $passed,
            'total'           => count($checks),
            'critical_failed' => array_values($critical),
            'ready'           => $critical === [],
        ];
    }

    private function check(string $key, bool $pass, string $detail, bool $critical): array
    {
        return ['key' => $key, 'pass' => $pass, 'detail' => $detail, 'critical' => $critical];
    }
}
