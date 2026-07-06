{{-- Design System: labelled input with validation + hint.
     Usage: <x-ui.input name="phone" :label="__('...')" type="tel" :value="old('phone')" required :hint="__('...')" /> --}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}@if ($required) <span class="text-danger">*</span>@endif
    </label>
    <input type="{{ $type }}"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ $value }}"
           @if ($required) required @endif
           {{ $attributes->merge(['class' => 'form-control' . (($errors ?? null)?->has($name) ? ' is-invalid' : '')]) }}>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
