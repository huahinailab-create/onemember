<?php

namespace App\Http\Controllers\DevTools;

use App\Models\User;
use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevUsersController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevUserService $service,
    ) {
        parent::__construct($audit);
    }

    public function index(Request $request): View
    {
        $users = [];
        $query = $request->get('q');
        if ($query) {
            $users = User::withTrashed()
                ->where(function ($q) use ($query) {
                    $q->where('email', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%");
                })
                ->with('merchant')
                ->limit(20)
                ->get();
        }
        return view('dev.users', compact('users', 'query'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->audit->log('user.force_delete', User::class, $user->id, ['email' => $user->email]);
        $this->service->deleteUser($user);
        return back()->with('success', "User {$user->email} permanently deleted.");
    }

    public function softDelete(User $user): RedirectResponse
    {
        $this->audit->log('user.soft_delete', User::class, $user->id, ['email' => $user->email]);
        $this->service->softDeleteUser($user);
        return back()->with('success', "User {$user->email} soft-deleted.");
    }

    public function restore(User $user): RedirectResponse
    {
        $this->audit->log('user.restore', User::class, $user->id, ['email' => $user->email]);
        $this->service->restoreUser($user);
        return back()->with('success', "User {$user->email} restored.");
    }

    public function verifyEmail(User $user): RedirectResponse
    {
        $this->audit->log('user.verify_email', User::class, $user->id);
        $this->service->verifyEmail($user);
        return back()->with('success', "Email verified for {$user->email}.");
    }

    public function unverifyEmail(User $user): RedirectResponse
    {
        $this->audit->log('user.unverify_email', User::class, $user->id);
        $this->service->unverifyEmail($user);
        return back()->with('success', "Email unverified for {$user->email}.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate(['password' => 'required|min:8']);
        $this->audit->log('user.reset_password', User::class, $user->id);
        $this->service->resetPassword($user, $request->password);
        return back()->with('success', "Password reset for {$user->email}.");
    }

    public function generateTempPassword(User $user): RedirectResponse
    {
        $this->audit->log('user.generate_temp_password', User::class, $user->id);
        $password = $this->service->generateTemporaryPassword($user);
        return back()->with('success', "Temporary password for {$user->email}: <code>{$password}</code>");
    }

    public function resendVerification(User $user): RedirectResponse
    {
        $this->audit->log('user.resend_verification', User::class, $user->id);
        $this->service->resendVerificationEmail($user);
        return back()->with('success', "Verification email queued for {$user->email}.");
    }

    public function loginAs(User $user): RedirectResponse
    {
        $this->audit->log('user.impersonate', User::class, $user->id, ['email' => $user->email]);
        session(['dev_impersonating_as' => $user->id, 'dev_original_user' => auth()->id()]);
        auth()->login($user);
        return redirect()->route('dashboard')->with('success', "Logged in as {$user->email}.");
    }

    public function clearFailedLogins(User $user): RedirectResponse
    {
        $this->audit->log('user.clear_failed_logins', User::class, $user->id);
        $this->service->clearFailedLoginAttempts($user);
        return back()->with('success', "Failed login attempts cleared for {$user->email}.");
    }

    public function deleteSessions(User $user): RedirectResponse
    {
        $this->audit->log('user.delete_sessions', User::class, $user->id);
        $this->service->deleteUserSessions($user);
        return back()->with('success', "Sessions deleted for {$user->email}.");
    }
}
