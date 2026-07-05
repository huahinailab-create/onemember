<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'password_changed_at' => 'datetime',
            'is_admin'            => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (User $user) {
            if ($user->isDirty('password')) {
                $user->password_changed_at = now();
            }
        });

        static::updated(function (User $user) {
            if ($user->wasChanged('password')) {
                app(\App\Services\SecurityLogger::class)->passwordChanged(
                    $user->id,
                    $user->email,
                    $user->merchant?->id
                );
            }
        });
    }

    public function merchant(): HasOne
    {
        return $this->hasOne(Merchant::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }
}
