<x-app-layout>
    <x-slot name="title">{{ __('Profile') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('Profile') }}</x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Profile Information --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-1">{{ __('Profile Information') }}</h5>
                        <p class="text-muted small mb-4">{{ __("Update your account's profile information and email address.") }}</p>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">{{ __('Name') }}</label>
                                <input id="name" name="name" type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}"
                                       required autofocus autocomplete="name">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-medium">Email</label>
                                <input id="email" name="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       required autocomplete="username">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-2">
                                        <p class="text-muted small mb-1">{{ __('Your email address is unverified.') }}</p>
                                        <button form="send-verification" class="btn btn-link btn-sm p-0">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                        @if (session('status') === 'verification-link-sent')
                                            <p class="text-success small mt-1">{{ __('A new verification link has been sent to your email address.') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                @if (session('status') === 'profile-updated')
                                    <span class="text-success small">{{ __('Saved.') }}</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-1">{{ __('Update Password') }}</h5>
                        <p class="text-muted small mb-4">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="mb-3">
                                <label for="update_password_current_password" class="form-label fw-medium">{{ __('Current Password') }}</label>
                                <input id="update_password_current_password" name="current_password" type="password"
                                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                       autocomplete="current-password">
                                @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="update_password_password" class="form-label fw-medium">{{ __('New Password') }}</label>
                                <input id="update_password_password" name="password" type="password"
                                       class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="update_password_password_confirmation" class="form-label fw-medium">{{ __('Confirm Password') }}</label>
                                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password_confirmation', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                @if (session('status') === 'password-updated')
                                    <span class="text-success small">{{ __('Saved.') }}</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="card shadow-sm border-danger-subtle mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-1 text-danger">{{ __('Delete Account') }}</h5>
                        <p class="text-muted small mb-4">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Delete Account Modal --}}
    <div class="modal fade" id="confirmUserDeletion" tabindex="-1"
         @if ($errors->userDeletion->isNotEmpty()) aria-hidden="false" @endif>
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Are you sure you want to delete your account?') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted small">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

                        <div class="mt-3">
                            <label for="delete_password" class="visually-hidden">{{ __('Password') }}</label>
                            <input id="delete_password" name="password" type="password"
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="{{ __('Password') }}">
                            @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('confirmUserDeletion')).show();
        });
    </script>
    @endif
</x-app-layout>
