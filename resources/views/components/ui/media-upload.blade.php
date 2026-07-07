{{--
    Design System: reusable premium image upload — drag & drop, live preview,
    crop + rotate (Cropper.js), replace/remove. Not Product-specific: any
    future module (merchant logo, staff avatar, supplier logo, gallery item,
    document thumbnail, ...) can drop this in with its own $name/$aspect.

    Progressive enhancement: the native <input type="file"> (and, when an
    image already exists, its plain "remove" checkbox) are always present
    and functional — that's `.media-upload-native-fallback`, visible by
    default. resources/js/product-image.js finds every [data-media-upload]
    root and only THEN hides that fallback and reveals `.media-upload-
    enhanced`. If JS fails to load, the native controls stay visible and
    the form still submits correctly.

    Usage:
      <x-ui.media-upload name="image" :current-url="$product?->imageUrl()" />
      <x-ui.media-upload name="logo" :aspect="1" :presets="['1:1' => 1]" remove-name="remove_logo" />
--}}
@props([
    'name' => 'image',
    'removeName' => 'remove_image',
    'currentUrl' => null,
    'currentLabel' => null,
    'recommended' => '1200 × 1200 px',
    'minimum' => '800 × 800 px',
    'formats' => 'JPG, PNG, WebP',
    'maxMb' => 2,
    'aspect' => 1,
    'presets' => null,
])

@php
    $presets ??= ['1:1' => 1, '4:5' => 0.8, '16:9' => 1.7777778];
    $options = [
        'aspect'  => (float) $aspect,
        'presets' => $presets,
        'maxMb'   => (float) $maxMb,
        'accept'  => ['image/jpeg', 'image/png', 'image/webp'],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'media-upload']) }}
     data-media-upload
     data-media-upload-current="{{ $currentUrl }}"
     data-media-upload-current-label="{{ $currentLabel }}"
     data-media-upload-options="{{ json_encode($options) }}">

    {{-- Native fallback — always functional; JS hides this once enhanced --}}
    <div class="media-upload-native-fallback">
        <input type="file"
               id="{{ $name }}"
               name="{{ $name }}"
               accept="image/jpeg,image/png,image/webp"
               class="media-upload-native-input form-control @error($name) is-invalid @enderror">
        @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

        @if ($currentUrl)
            <div class="form-check mt-2">
                <input class="form-check-input media-upload-remove-checkbox" type="checkbox"
                       id="{{ $removeName }}" name="{{ $removeName }}" value="1">
                <label class="form-check-label" for="{{ $removeName }}">{{ __('media.remove') }}</label>
            </div>
        @else
            <input type="checkbox" class="media-upload-remove-checkbox d-none"
                   name="{{ $removeName }}" value="1">
        @endif

        <div class="form-text media-upload-hint">
            {{ __('media.upload_recommended', ['size' => $recommended]) }} ·
            {{ __('media.upload_minimum', ['size' => $minimum]) }} ·
            {{ $formats }} · {{ __('media.upload_max_size', ['mb' => $maxMb]) }}
        </div>
    </div>

    {{-- Enhanced UI — hidden until JS confirms it can run --}}
    <div class="media-upload-enhanced" hidden>

        {{-- Dropzone --}}
        <div class="media-upload-dropzone" tabindex="0" role="button"
             aria-label="{{ __('media.upload_dropzone_label') }}">
            <i class="bi bi-cloud-arrow-up media-upload-dropzone-icon" aria-hidden="true"></i>
            <div class="media-upload-dropzone-text">{{ __('media.upload_dropzone_text') }}</div>
            <div class="media-upload-guidance">
                <span>{{ __('media.upload_recommended', ['size' => $recommended]) }}</span>
                <span>{{ __('media.upload_minimum', ['size' => $minimum]) }}</span>
                <span>{{ $formats }}</span>
                <span>{{ __('media.upload_max_size', ['mb' => $maxMb]) }}</span>
            </div>
        </div>

        {{-- Selected / current image workspace --}}
        <div class="media-upload-workspace d-none">
            <div class="media-upload-crop-stage">
                <img class="media-upload-crop-image" alt="">
            </div>

            <div class="media-upload-aspect-presets" role="group" aria-label="{{ __('media.aspect_presets_label') }}">
                @foreach ($presets as $label => $ratio)
                    <button type="button" class="btn btn-sm btn-outline-secondary media-upload-aspect-btn"
                            data-ratio="{{ $ratio }}">{{ $label }}</button>
                @endforeach
            </div>

            <div class="media-upload-rotate-controls">
                <button type="button" class="btn btn-sm btn-outline-secondary media-upload-rotate-left"
                        aria-label="{{ __('media.rotate_left') }}">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary media-upload-rotate-right"
                        aria-label="{{ __('media.rotate_right') }}">
                    <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                </button>
            </div>

            <div class="media-upload-meta">
                <div class="media-upload-filename text-truncate"></div>
                <div class="media-upload-dimensions text-muted small"></div>
                <div class="media-upload-filesize text-muted small"></div>
            </div>

            <div class="media-upload-actions">
                <button type="button" class="btn btn-sm btn-outline-primary media-upload-replace">
                    <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>{{ __('media.replace') }}
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger media-upload-remove">
                    <i class="bi bi-trash me-1" aria-hidden="true"></i>{{ __('media.remove') }}
                </button>
            </div>
        </div>

        <div class="invalid-feedback d-block media-upload-error" hidden></div>
    </div>
</div>
