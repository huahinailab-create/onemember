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

    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        $this->logger->loginSucceeded(
            $user->id,
            $user->email,
            $user->merchant?->id
        );
    }

    public function handleFailed(Failed $event): void
    {
        $this->logger->loginFailed($event->credentials['email'] ?? '');
    }

    public function handleLogout(Logout $event): void
    {
        if ($user = $event->user) {
            $this->logger->logout($user->id, $user->email, $user->merchant?->id);
        }
    }

    public function handlePasswordResetLinkSent(PasswordResetLinkSent $event): void
    {
        $this->logger->passwordResetRequested($event->user->email);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $user = $event->user;
        $this->logger->passwordResetCompleted($user->id, $user->email, $user->merchant?->id);
    }

    public function handleVerified(Verified $event): void
    {
        $user = $event->user;
        $this->logger->emailVerified($user->id, $user->email, $user->merchant?->id);
    }

    public function handleRegistered(Registered $event): void
    {
        $user = $event->user;
        $this->logger->merchantRegistered($user->id, $user->email);
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
