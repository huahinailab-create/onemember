{{-- Design System: labelled select with validation.
     Usage: <x-ui.select name="country" :label="__('...')" :options="config('countries.list')" :selected="old('country', $m->country)" required /> --}}
@props(['name', 'label', 'options' => [], 'selected' => null, 'hint' => null, 'required' => false, 'placeholder' => null])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}@if ($required) <span class="text-danger">*</span>@endif
    </label>
    <select id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->merge(['class' => 'form-select' . (($errors ?? null)?->has($name) ? ' is-invalid' : '')]) }}>
        @if ($placeholder)
            <option value="" disabled {{ $selected === null || $selected === '' ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" {{ (string) $selected === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
