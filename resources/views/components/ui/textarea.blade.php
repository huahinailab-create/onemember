{{-- Design System: labelled textarea with validation. --}}
@props(['name', 'label', 'value' => null, 'hint' => null, 'required' => false, 'rows' => 3])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}@if ($required) <span class="text-danger">*</span>@endif
    </label>
    <textarea id="{{ $name }}"
              name="{{ $name }}"
              rows="{{ $rows }}"
              @if ($required) required @endif
              {{ $attributes->merge(['class' => 'form-control' . (($errors ?? null)?->has($name) ? ' is-invalid' : '')]) }}>{{ $value }}</textarea>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
