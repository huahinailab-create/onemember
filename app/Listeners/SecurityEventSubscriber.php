<?php

namespace App\Listeners;

use App\Services\SecurityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;

/**
 * Subscribes to all built-in Laravel auth events and forwards them
 * to the SecurityLogger service. Adding a new event means adding one
 * handler method and one $events->listen() call in subscribe().
 */
class SecurityEventSubscriber
{
    public function __construct(private readonly SecurityLogger $logger) {}

    /**
     * CUSTOMER-001A: auth events now fire for two models — merchant User
     * and Customer. Customers may have no email (phone-only accounts), so
     * the log identifier falls back phone -> key; only merchant Users have
     * a merchant to attribute.
     */
    private function identify(mixed $user): array
    {
        $identifier = $user->email
            ?? $user->phone
            ?? (class_basename($user) . ':' . $user->getKey());

        $merchantId = $user instanceof \App\Models\User ? $user->merchant?->id : null;

        return [$identifier, $merchantId];
    }

    public function handleLogin(Login $event): void
    {
        [$identifier, $merchantId] = $this->identify($event->user);
        $this->logger->loginSucceeded($event->user->id, $identifier, $merchantId);
    }

    public function handleFailed(Failed $event): void
    {
        $this->logger->loginFailed($event->credentials['email'] ?? '');
    }

    public function handleLogout(Logout $event): void
    {
        if ($user = $event->user) {
            [$identifier, $merchantId] = $this->identify($user);
            $this->logger->logout($user->id, $identifier, $merchantId);
        }
    }

    public function handlePasswordResetLinkSent(PasswordResetLinkSent $event): void
    {
        $this->logger->passwordResetRequested($event->user->email);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        [$identifier, $merchantId] = $this->identify($event->user);
        $this->logger->passwordResetCompleted($event->user->id, $identifier, $merchantId);
    }

    public function handleVerified(Verified $event): void
    {
        [$identifier, $merchantId] = $this->identify($event->user);
        $this->logger->emailVerified($event->user->id, $identifier, $merchantId);
    }

    public function handleRegistered(Registered $event): void
    {
        // merchantRegistered stays merchant-only; customer registration has
        // its own flow and does not dispatch Registered.
        if ($event->user instanceof \App\Models\User) {
            $this->logger->merchantRegistered($event->user->id, $event->user->email);
        }
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Login::class,                [static::class, 'handleLogin']);
        $events->listen(Failed::class,               [static::class, 'handleFailed']);
        $events->listen(Logout::class,               [static::class, 'handleLogout']);
        $events->listen(PasswordResetLinkSent::class, [static::class, 'handlePasswordResetLinkSent']);
        $events->listen(PasswordReset::class,        [static::class, 'handlePasswordReset']);
        $events->listen(Verified::class,             [static::class, 'handleVerified']);
        $events->listen(Registered::class,           [static::class, 'handleRegistered']);
    }
}
