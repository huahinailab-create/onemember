@props(['dropup' => false])
@php
    $current = app()->getLocale();
    $labels  = ['en' => 'EN', 'th' => 'ไทย'];
    $other   = $current === 'en' ? 'th' : 'en';
@endphp

<div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle lang-switcher-btn"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="font-size:0.8rem;padding:0.25rem 0.625rem;border-color:rgba(26,46,90,0.2);color:#1A2E5A;">
        <i class="bi bi-translate me-1"></i>{{ $labels[$current] ?? 'EN' }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end {{ $dropup ? 'dropup' : '' }}" style="min-width:7rem;">
        <li>
            <form method="POST" action="{{ route('locale.switch') }}">
                @csrf
                <input type="hidden" name="locale" value="en">
                <button type="submit"
                        class="dropdown-item d-flex align-items-center gap-2 {{ $current === 'en' ? 'fw-semibold' : '' }}"
                        style="font-size:0.875rem;">
                    @if($current === 'en') <i class="bi bi-check2" style="color:#FF1585;font-size:0.75rem;"></i> @else <span style="width:0.875rem;display:inline-block;"></span> @endif
                    English
                </button>
            </form>
        </li>
        <li>
            <form method="POST" action="{{ route('locale.switch') }}">
                @csrf
                <input type="hidden" name="locale" value="th">
                <button type="submit"
                        class="dropdown-item d-flex align-items-center gap-2 {{ $current === 'th' ? 'fw-semibold' : '' }}"
                        style="font-size:0.875rem;">
                    @if($current === 'th') <i class="bi bi-check2" style="color:#FF1585;font-size:0.75rem;"></i> @else <span style="width:0.875rem;display:inline-block;"></span> @endif
                    ภาษาไทย
                </button>
            </form>
        </li>
    </ul>
</div>
