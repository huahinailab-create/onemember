<?php

use App\Http\Controllers\DevTools\DevDashboardController;
use App\Http\Controllers\DevTools\DevDatabaseController;
use App\Http\Controllers\DevTools\DevDangerController;
use App\Http\Controllers\DevTools\DevDemoResetController;
use App\Http\Controllers\DevTools\DevEnvInspectorController;
use App\Http\Controllers\DevTools\DevEnvironmentController;
use App\Http\Controllers\DevTools\DevFeatureFlagsController;
use App\Http\Controllers\DevTools\DevHealthController;
use App\Http\Controllers\DevTools\DevHelpersController;
use App\Http\Controllers\DevTools\DevLogViewerController;
use App\Http\Controllers\DevTools\DevMailController;
use App\Http\Controllers\DevTools\DevMailInspectorController;
use App\Http\Controllers\DevTools\DevMembersController;
use App\Http\Controllers\DevTools\DevMerchantsController;
use App\Http\Controllers\DevTools\DevPerformanceController;
use App\Http\Controllers\DevTools\DevQuickActionsController;
use App\Http\Controllers\DevTools\DevQueueController;
use App\Http\Controllers\DevTools\DevQueueInspectorController;
use App\Http\Controllers\DevTools\DevStorageController;
use App\Http\Controllers\DevTools\DevUsersController;
use Illuminate\Support\Facades\Route;

// ── Dashboard (root) ───────────────────────────────────────────────────────
Route::get('/', [DevDashboardController::class, 'index'])->name('dashboard');

// ── Quick Actions ──────────────────────────────────────────────────────────
Route::get('/quick-actions',                   [DevQuickActionsController::class, 'index'])->name('quick-actions');
Route::post('/quick-actions/demo-merchant',    [DevQuickActionsController::class, 'createDemoMerchant'])->name('quick-actions.demo-merchant');
Route::post('/quick-actions/members',          [DevQuickActionsController::class, 'generateMembers'])->name('quick-actions.members');
Route::post('/quick-actions/purchases',        [DevQuickActionsController::class, 'generatePurchases'])->name('quick-actions.purchases');
Route::post('/quick-actions/points',           [DevQuickActionsController::class, 'generatePoints'])->name('quick-actions.points');
Route::post('/quick-actions/stamps',           [DevQuickActionsController::class, 'generateStamps'])->name('quick-actions.stamps');
Route::post('/quick-actions/redemptions',      [DevQuickActionsController::class, 'generateRedemptions'])->name('quick-actions.redemptions');
Route::post('/quick-actions/birthdays',        [DevQuickActionsController::class, 'generateBirthdays'])->name('quick-actions.birthdays');
Route::post('/quick-actions/notifications',    [DevQuickActionsController::class, 'generateNotifications'])->name('quick-actions.notifications');
Route::post('/quick-actions/reset-demo',       [DevQuickActionsController::class, 'resetDemo'])->name('quick-actions.reset-demo');

// ── Mail Inspector ─────────────────────────────────────────────────────────
Route::get('/mail-inspector',                  [DevMailInspectorController::class, 'index'])->name('mail-inspector');
Route::post('/mail-inspector/send-test',       [DevMailInspectorController::class, 'sendTest'])->name('mail-inspector.send-test');
Route::post('/mail-inspector/send-verification',[DevMailInspectorController::class, 'sendVerification'])->name('mail-inspector.send-verification');
Route::post('/mail-inspector/test-resend',     [DevMailInspectorController::class, 'testResend'])->name('mail-inspector.test-resend');
Route::post('/mail-inspector/check-key',       [DevMailInspectorController::class, 'checkApiKey'])->name('mail-inspector.check-key');

// ── Queue Inspector ────────────────────────────────────────────────────────
Route::get('/queue-inspector',                 [DevQueueInspectorController::class, 'index'])->name('queue-inspector');
Route::post('/queue-inspector/restart',        [DevQueueInspectorController::class, 'restart'])->name('queue-inspector.restart');
Route::post('/queue-inspector/retry-failed',   [DevQueueInspectorController::class, 'retryFailed'])->name('queue-inspector.retry-failed');
Route::delete('/queue-inspector/failed',       [DevQueueInspectorController::class, 'deleteFailed'])->name('queue-inspector.delete-failed');

// ── Environment Inspector ──────────────────────────────────────────────────
Route::get('/env-inspector',                   [DevEnvInspectorController::class, 'index'])->name('env-inspector');

// ── Performance ────────────────────────────────────────────────────────────
Route::get('/performance',                     [DevPerformanceController::class, 'index'])->name('performance');
Route::post('/performance/run',                [DevPerformanceController::class, 'run'])->name('performance.run');

// ── Log Viewer ─────────────────────────────────────────────────────────────
Route::get('/logs',                            [DevLogViewerController::class, 'index'])->name('logs');
Route::get('/logs/download',                   [DevLogViewerController::class, 'download'])->name('logs.download');
Route::delete('/logs',                         [DevLogViewerController::class, 'clear'])->name('logs.clear');

// ── Demo Reset ─────────────────────────────────────────────────────────────
Route::get('/demo-reset',                      [DevDemoResetController::class, 'index'])->name('demo-reset');
Route::delete('/demo-reset',                   [DevDemoResetController::class, 'reset'])->name('demo-reset.reset');

// ── Feature Flags ──────────────────────────────────────────────────────────
Route::get('/feature-flags',                   [DevFeatureFlagsController::class, 'index'])->name('feature-flags');

// ── Users ──────────────────────────────────────────────────────────────────
Route::get('/users',                      [DevUsersController::class, 'index'])->name('users');
Route::delete('/users/{user}',            [DevUsersController::class, 'destroy'])->name('users.destroy');
Route::post('/users/{user}/soft-delete',  [DevUsersController::class, 'softDelete'])->name('users.soft-delete');
Route::post('/users/{user}/restore',      [DevUsersController::class, 'restore'])->name('users.restore');
Route::post('/users/{user}/verify-email', [DevUsersController::class, 'verifyEmail'])->name('users.verify-email');
Route::post('/users/{user}/unverify-email',[DevUsersController::class, 'unverifyEmail'])->name('users.unverify-email');
Route::post('/users/{user}/reset-password',[DevUsersController::class, 'resetPassword'])->name('users.reset-password');
Route::post('/users/{user}/temp-password',[DevUsersController::class, 'generateTempPassword'])->name('users.temp-password');
Route::post('/users/{user}/resend-verification',[DevUsersController::class, 'resendVerification'])->name('users.resend-verification');
Route::post('/users/{user}/login-as',     [DevUsersController::class, 'loginAs'])->name('users.login-as');
Route::post('/users/{user}/clear-failed-logins',[DevUsersController::class, 'clearFailedLogins'])->name('users.clear-failed-logins');
Route::post('/users/{user}/delete-sessions',[DevUsersController::class, 'deleteSessions'])->name('users.delete-sessions');

// ── Members ────────────────────────────────────────────────────────────────
Route::get('/members',                       [DevMembersController::class, 'index'])->name('members');
Route::delete('/members/{member}',           [DevMembersController::class, 'destroy'])->name('members.destroy');
Route::post('/members/{member}/archive',     [DevMembersController::class, 'archive'])->name('members.archive');
Route::post('/members/{member}/restore',     [DevMembersController::class, 'restore'])->name('members.restore');
Route::post('/members/{member}/reset-points',[DevMembersController::class, 'resetPoints'])->name('members.reset-points');
Route::post('/members/{member}/set-points',  [DevMembersController::class, 'setPoints'])->name('members.set-points');
Route::post('/members/{member}/add-points',  [DevMembersController::class, 'addPoints'])->name('members.add-points');
Route::post('/members/{member}/deduct-points',[DevMembersController::class, 'deductPoints'])->name('members.deduct-points');
Route::post('/members/{member}/reset-stamps',[DevMembersController::class, 'resetStamps'])->name('members.reset-stamps');
Route::delete('/members/{member}/transactions',[DevMembersController::class, 'deleteTransactions'])->name('members.delete-transactions');
Route::delete('/members/{member}/redemptions',[DevMembersController::class, 'deleteRedemptions'])->name('members.delete-redemptions');
Route::delete('/members/{member}/notifications',[DevMembersController::class, 'deleteNotifications'])->name('members.delete-notifications');
Route::post('/members/{member}/regenerate-qr',[DevMembersController::class, 'regenerateQr'])->name('members.regenerate-qr');

// ── Merchants ──────────────────────────────────────────────────────────────
Route::get('/merchants',                          [DevMerchantsController::class, 'index'])->name('merchants');
Route::delete('/merchants/{merchant}',            [DevMerchantsController::class, 'destroy'])->name('merchants.destroy');
Route::post('/merchants/{merchant}/archive',      [DevMerchantsController::class, 'archive'])->name('merchants.archive');
Route::post('/merchants/{merchant}/restore',      [DevMerchantsController::class, 'restore'])->name('merchants.restore');
Route::post('/merchants/{merchant}/reset-onboarding',[DevMerchantsController::class, 'resetOnboarding'])->name('merchants.reset-onboarding');
Route::post('/merchants/{merchant}/reset-subscription',[DevMerchantsController::class, 'resetSubscription'])->name('merchants.reset-subscription');
Route::post('/merchants/{merchant}/change-plan',  [DevMerchantsController::class, 'changePlan'])->name('merchants.change-plan');
Route::post('/merchants/{merchant}/activate-trial',[DevMerchantsController::class, 'activateTrial'])->name('merchants.activate-trial');
Route::post('/merchants/{merchant}/expire-trial', [DevMerchantsController::class, 'expireTrial'])->name('merchants.expire-trial');
Route::post('/merchants/{merchant}/reset-billing',[DevMerchantsController::class, 'resetBilling'])->name('merchants.reset-billing');
Route::post('/merchants/{merchant}/reset-loyalty',[DevMerchantsController::class, 'resetLoyaltyProgram'])->name('merchants.reset-loyalty');
Route::post('/merchants/{merchant}/reset-campaigns',[DevMerchantsController::class, 'resetCampaigns'])->name('merchants.reset-campaigns');
Route::delete('/merchants/{merchant}/data',       [DevMerchantsController::class, 'deleteData'])->name('merchants.delete-data');

// ── Mail ───────────────────────────────────────────────────────────────────
Route::get('/mail',              [DevMailController::class, 'index'])->name('mail');
Route::post('/mail/send',        [DevMailController::class, 'send'])->name('mail.send');
Route::post('/mail/test-resend', [DevMailController::class, 'testResend'])->name('mail.test-resend');

// ── Database ───────────────────────────────────────────────────────────────
Route::get('/database',            [DevDatabaseController::class, 'index'])->name('database');
Route::post('/database/command',   [DevDatabaseController::class, 'runCommand'])->name('database.command');
Route::post('/database/fresh-seed',[DevDatabaseController::class, 'freshSeed'])->name('database.fresh-seed');

// ── Queue ──────────────────────────────────────────────────────────────────
Route::get('/queue',                [DevQueueController::class, 'index'])->name('queue');
Route::post('/queue/retry-failed',  [DevQueueController::class, 'retryFailed'])->name('queue.retry-failed');
Route::delete('/queue/failed',      [DevQueueController::class, 'deleteFailed'])->name('queue.delete-failed');
Route::post('/queue/restart',       [DevQueueController::class, 'restart'])->name('queue.restart');

// ── Storage ────────────────────────────────────────────────────────────────
Route::get('/storage',               [DevStorageController::class, 'index'])->name('storage');
Route::delete('/storage/logs',       [DevStorageController::class, 'clearLogs'])->name('storage.clear-logs');
Route::get('/storage/logs/download', [DevStorageController::class, 'downloadLog'])->name('storage.download-log');
Route::delete('/storage/sessions',   [DevStorageController::class, 'clearSessions'])->name('storage.clear-sessions');
Route::post('/storage/link',         [DevStorageController::class, 'storageLink'])->name('storage.link');

// ── Helpers ────────────────────────────────────────────────────────────────
Route::get('/helpers',                       [DevHelpersController::class, 'index'])->name('helpers');
Route::post('/helpers/generate-members',     [DevHelpersController::class, 'generateMembers'])->name('helpers.generate-members');
Route::post('/helpers/generate-transactions',[DevHelpersController::class, 'generateTransactions'])->name('helpers.generate-transactions');

// ── Environment ────────────────────────────────────────────────────────────
Route::get('/environment', [DevEnvironmentController::class, 'index'])->name('environment');

// ── System Health ──────────────────────────────────────────────────────────
Route::get('/health', [DevHealthController::class, 'index'])->name('health');

// ── Danger Zone ────────────────────────────────────────────────────────────
Route::get('/danger',                         [DevDangerController::class, 'index'])->name('danger');
Route::delete('/danger/members',              [DevDangerController::class, 'truncateMembers'])->name('danger.truncate-members');
Route::delete('/danger/users',                [DevDangerController::class, 'truncateUsers'])->name('danger.truncate-users');
Route::delete('/danger/nuke',                 [DevDangerController::class, 'nukeDatabaseExceptCurrentUser'])->name('danger.nuke');
