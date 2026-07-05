<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MerchantStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Redemption;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Platform totals ──────────────────────────────────────────────────
        $totalMerchants      = Merchant::count();
        $activeMerchants     = Merchant::where('status', MerchantStatus::Active)->count();
        $inactiveMerchants   = Merchant::whereIn('status', [MerchantStatus::Inactive, MerchantStatus::Suspended])->count();
        $trialMerchants      = Merchant::where('subscription_status', SubscriptionStatus::Trial)->count();
        $paidMerchants       = Merchant::where('subscription_status', SubscriptionStatus::Active)->count();
        $freeMerchants       = Merchant::where('subscription_plan', SubscriptionPlan::Free)->count();

        $totalMembers        = Member::count();
        $membersToday        = Member::whereDate('created_at', today())->count();

        $totalTransactions   = Transaction::count();
        $transactionsToday   = Transaction::whereDate('created_at', today())->count();

        $rewardsRedeemed     = Redemption::count();
        $redemptionsToday    = Redemption::whereDate('created_at', today())->count();

        // ── Recent registrations ─────────────────────────────────────────────
        $recentMerchants = Merchant::with('owner')
            ->latest()
            ->take(10)
            ->get();

        // ── Analytics: new merchants ─────────────────────────────────────────
        $newMerchantsToday     = Merchant::whereDate('created_at', today())->count();
        $newMerchantsThisWeek  = Merchant::where('created_at', '>=', now()->startOfWeek())->count();
        $newMerchantsThisMonth = Merchant::where('created_at', '>=', now()->startOfMonth())->count();

        // ── Analytics: new members ───────────────────────────────────────────
        $newMembersToday     = Member::whereDate('created_at', today())->count();
        $newMembersThisWeek  = Member::where('created_at', '>=', now()->startOfWeek())->count();
        $newMembersThisMonth = Member::where('created_at', '>=', now()->startOfMonth())->count();

        // ── Analytics: top performers ────────────────────────────────────────
        $topByMembers = Merchant::withCount('members')
            ->orderBy('members_count', 'desc')
            ->take(5)
            ->get();

        $topByTransactions = Merchant::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();

        // ── Analytics: attention needed ──────────────────────────────────────
        $zeroMembers     = Merchant::doesntHave('members')->count();
        $notOnboarded    = Merchant::whereNull('onboarding_completed_at')->count();
        $trialEndingSoon = Merchant::where('subscription_status', SubscriptionStatus::Trial)
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->count();

        // ── Platform health ──────────────────────────────────────────────────
        $health = $this->buildHealthStatus();

        // ── Activation funnel ────────────────────────────────────────────────
        $funnel = $this->buildActivationFunnel();

        // ── Geographic: top postal codes ─────────────────────────────────────
        $topPostalCodes = Member::whereNotNull('postal_code')
            ->selectRaw('postal_code, COUNT(*) as member_count')
            ->groupBy('postal_code')
            ->orderByDesc('member_count')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalMerchants', 'activeMerchants', 'inactiveMerchants',
            'trialMerchants', 'paidMerchants', 'freeMerchants',
            'totalMembers', 'membersToday',
            'totalTransactions', 'transactionsToday',
            'rewardsRedeemed', 'redemptionsToday',
            'recentMerchants',
            'newMerchantsToday', 'newMerchantsThisWeek', 'newMerchantsThisMonth',
            'newMembersToday', 'newMembersThisWeek', 'newMembersThisMonth',
            'topByMembers', 'topByTransactions',
            'zeroMembers', 'notOnboarded', 'trialEndingSoon',
            'health', 'funnel', 'topPostalCodes',
        ));
    }

    // ── Platform health ──────────────────────────────────────────────────────

    private function buildHealthStatus(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'email'    => $this->checkEmail(),
            'queue'    => $this->checkQueue(),
            'storage'  => $this->checkStorage(),
            'scheduler' => [
                'status'  => 'unknown',
                'label'   => 'Needs heartbeat',
                'detail'  => 'Configure scheduler heartbeat monitoring in a future sprint.',
                'note'    => true,
            ],
            'backup' => [
                'status' => 'unknown',
                'label'  => 'Not configured',
                'detail' => 'No backup solution configured yet.',
                'note'   => true,
            ],
            'version' => [
                'status' => 'ok',
                'label'  => config('app.version', '1.0'),
                'detail' => 'Laravel ' . app()->version() . ' · PHP ' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
            ],
        ];
    }

    private function checkDatabase(): array
    {
        try {
            DB::select('SELECT 1');
            $driver = DB::connection()->getDriverName();
            return [
                'status' => 'ok',
                'label'  => 'Healthy',
                'detail' => ucfirst($driver),
            ];
        } catch (Throwable $e) {
            return [
                'status' => 'error',
                'label'  => 'Unreachable',
                'detail' => 'Connection failed.',
            ];
        }
    }

    private function checkEmail(): array
    {
        $mailer = config('mail.default', 'log');
        // In production, any non-log/array driver indicates real mail is configured.
        $configured = ! in_array($mailer, ['log', 'array']);
        return [
            'status' => $configured ? 'ok' : 'warn',
            'label'  => $configured ? 'Configured' : 'Dev mode',
            'detail' => 'Driver: ' . $mailer,
            'note'   => ! $configured,
        ];
    }

    private function checkQueue(): array
    {
        $driver = config('queue.default', 'sync');
        $async  = $driver !== 'sync';
        return [
            'status' => $async ? 'ok' : 'warn',
            'label'  => $async ? 'Async' : 'Sync',
            'detail' => 'Driver: ' . $driver . ($async ? '' : ' — jobs run inline'),
            'note'   => ! $async,
        ];
    }

    private function checkStorage(): array
    {
        try {
            $free  = disk_free_space(storage_path());
            $total = disk_total_space(storage_path());
            $usedPct = $total > 0 ? round((($total - $free) / $total) * 100) : 0;
            $status = $usedPct >= 90 ? 'error' : ($usedPct >= 75 ? 'warn' : 'ok');
            return [
                'status' => $status,
                'label'  => $usedPct . '% used',
                'detail' => 'Free: ' . $this->formatBytes($free),
            ];
        } catch (Throwable) {
            return ['status' => 'unknown', 'label' => 'Unknown', 'detail' => 'Could not read disk.'];
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_073_741_824) {
            return round($bytes / 1_073_741_824, 1) . ' GB';
        }
        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 1) . ' MB';
        }
        return round($bytes / 1_024, 1) . ' KB';
    }

    // ── Activation funnel ────────────────────────────────────────────────────

    private function buildActivationFunnel(): array
    {
        $registered    = Merchant::count();
        $onboarded     = Merchant::whereNotNull('onboarding_completed_at')->count();
        $hasCampaign   = Merchant::has('loyaltyPrograms')->count();
        $hasMember     = Merchant::has('members')->count();
        $hasTransaction = Merchant::has('transactions')->count();
        $converted     = Merchant::where('subscription_status', SubscriptionStatus::Active)->count();

        $pct = fn ($n, $base) => $base > 0 ? round(($n / $base) * 100) : 0;

        return [
            ['label' => 'Registered',           'count' => $registered,    'pct_of_total' => 100,                         'pct_of_prev' => null],
            ['label' => 'Completed onboarding', 'count' => $onboarded,     'pct_of_total' => $pct($onboarded, $registered),    'pct_of_prev' => $pct($onboarded, $registered)],
            ['label' => 'Created first campaign','count' => $hasCampaign,   'pct_of_total' => $pct($hasCampaign, $registered),  'pct_of_prev' => $pct($hasCampaign, $onboarded)],
            ['label' => 'Added first member',   'count' => $hasMember,     'pct_of_total' => $pct($hasMember, $registered),    'pct_of_prev' => $pct($hasMember, $hasCampaign)],
            ['label' => 'First transaction',    'count' => $hasTransaction, 'pct_of_total' => $pct($hasTransaction, $registered), 'pct_of_prev' => $pct($hasTransaction, $hasMember)],
            ['label' => 'Converted to paid',    'count' => $converted,     'pct_of_total' => $pct($converted, $registered),    'pct_of_prev' => $pct($converted, $hasTransaction)],
        ];
    }
}
