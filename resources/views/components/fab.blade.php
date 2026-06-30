{{--
    FAB (Floating Action Button) — mobile only (hidden ≥768px via CSS).
    Links to Add Member as the primary POS quick action.
--}}
@auth
    @if (Auth::user()->merchant)
        <a href="{{ route('members.create') }}"
           class="fab d-md-none"
           title="{{ __('mobile.fab_add_member') }}"
           aria-label="{{ __('mobile.fab_add_member') }}">
            <i class="bi bi-person-plus-fill"></i>
        </a>
    @endif
@endauth
