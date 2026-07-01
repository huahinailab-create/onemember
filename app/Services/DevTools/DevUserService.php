<?php

namespace App\Services\DevTools;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DevUserService
{
    public function search(string $query): \Illuminate\Database\Eloquent\Collection
    {
        return User::withTrashed()
            ->where('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(20)
            ->get();
    }

    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->merchant?->forceDelete();
            $user->forceDelete();
        });
    }

    public function softDeleteUser(User $user): void
    {
        $user->delete();
    }

    public function restoreUser(User $user): void
    {
        $user->restore();
    }

    public function verifyEmail(User $user): void
    {
        $user->markEmailAsVerified();
    }

    public function unverifyEmail(User $user): void
    {
        $user->forceFill(['email_verified_at' => null])->save();
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->forceFill(['password' => Hash::make($password)])->save();
    }

    public function generateTemporaryPassword(User $user): string
    {
        $password = 'Temp' . strtoupper(Str::random(6)) . '!1';
        $user->forceFill(['password' => Hash::make($password)])->save();
        return $password;
    }

    public function resendVerificationEmail(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    public function clearFailedLoginAttempts(User $user): void
    {
        DB::table('failed_login_attempts')
            ->where('email', $user->email)
            ->delete();
    }

    public function deleteUserSessions(User $user): void
    {
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();
    }
}
