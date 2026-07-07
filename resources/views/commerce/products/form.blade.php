<x-app-layout>
    <x-slot name="title">{{ $product ? __('commerce.edit_product') : __('commerce.add_product') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('commerce.products_title') }}</x-slot>

    <div class="page-header">
        <h1>{{ $product ? __('commerce.edit_product') : __('commerce.add_product') }}</h1>
        <p>
            <a href="{{ route('commerce.products.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>{{ __('commerce.back_to_products') }}
            </a>
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data"
                          action="{{ $product ? route('commerce.products.update', $product) : route('commerce.products.store') }}">
                        @csrf
                        @if ($product) @method('PUT') @endif

                        {{-- ── General ─────────────────────────────── (OMEGA-001B) --}}
                        <h2 class="omega-form-section">{{ __('commerce.section_general') }}</h2>

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('commerce.field_name') }} <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" required maxlength="150"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product?->name) }}">
                            <div class="form-text">{{ __('commerce.name_hint') }}</div>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('commerce.field_description') }}</label>
                            <textarea id="description" name="description" rows="3" maxlength="1000"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $product?->description) }}</textarea>
                            <div class="form-text">{{ __('commerce.description_hint') }}</div>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- ── Image ───────────────────────────────── --}}
                        <h2 class="omega-form-section">{{ __('commerce.section_image') }}</h2>

                        {{-- OMEGA-001A — premium product image manager (one main image) --}}
                        @php
                            $omegaStrings = [
                                'error_type'      => __('commerce.image_error_type'),
                                'error_size'      => __('commerce.image_error_size'),
                                'warning_low_res' => __('commerce.image_warning_low_res'),
                            ];
                        @endphp
                        <div class="mb-3" data-omega-image data-strings="{{ json_encode($omegaStrings) }}">
                            <label for="image" class="form-label">{{ __('commerce.field_image') }}</label>

                            {{-- Current (already stored) image — edit mode --}}
                            @if ($product?->imageUrl())
                                <div class="d-flex align-items-center gap-3 mb-2 p-2 border rounded" data-omega-current>
                                    <div class="commerce-product-thumb commerce-product-thumb-lg flex-shrink-0">
                                        <img src="{{ $product->imageUrl() }}" alt="{{ __('commerce.image_current') }}: {{ $product->name }}">
                                    </div>
                                    <div class="small text-muted flex-grow-1">{{ __('commerce.image_current') }}</div>
                                </div>
                            @endif

                            {{-- Upload card: drag & drop / click / keyboard / mobile camera --}}
                            <div class="omega-dropzone {{ $product?->imageUrl() ? '' : '' }}"
                                 data-omega-dropzone
                                 role="button" tabindex="0"
                                 aria-label="{{ __('commerce.image_dropzone_aria') }}">
                                <i class="bi bi-cloud-arrow-up omega-dropzone-icon" aria-hidden="true"></i>
                                <div class="fw-medium">
                                    {{ $product?->imageUrl() ? __('commerce.image_replace_cta') : __('commerce.image_drop_cta') }}
                                </div>
                                <div class="text-muted small mt-1">{{ __('commerce.image_guidance_size') }}</div>
                                <div class="text-muted small">{{ __('commerce.image_guidance_min') }}</div>
                                <div class="text-muted small">{{ __('commerce.image_guidance_formats') }}</div>
                            </div>
                            {{-- Plain input stays visible without JS (progressive enhancement);
                                 the module hides it and drives it via the card. --}}
                            <input type="file" id="image" name="image"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="form-control mt-2 @error('image') is-invalid @enderror"
                                   data-omega-input
                                   aria-describedby="image-guidance">
                            <span id="image-guidance" class="visually-hidden">{{ __('commerce.image_guidance_size') }} {{ __('commerce.image_guidance_formats') }}</span>
                            <input type="hidden" name="remove_image" value="" data-omega-remove-flag>
                            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                            {{-- Live preview + toolbar --}}
                            <div class="d-none" data-omega-preview>
                                <div class="omega-preview-frame">
                                    <img src="" alt="{{ __('commerce.image_preview_alt') }}" data-omega-preview-img>
                                </div>
                                <div class="text-muted small mt-1" data-omega-meta aria-live="polite"></div>
                            </div>

                            <div class="alert alert-warning py-2 small mt-2 d-none" data-omega-warning role="status"></div>
                            <div class="alert alert-danger py-2 small mt-2 d-none" data-omega-error role="alert"></div>

                            {{-- Editor stage (Cropper.js mounts here on demand) --}}
                            <div class="omega-editor d-none mt-2" data-omega-editor>
                                <div class="omega-editor-stage" data-omega-stage></div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-2" data-omega-toolbar>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-omega-action="crop">
                                    <i class="bi bi-crop me-1" aria-hidden="true"></i>{{ __('commerce.image_crop') }}
                                </button>
                                <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('commerce.image_aspect_aria') }}">
                                    <button type="button" class="btn btn-outline-secondary" data-omega-action="aspect" data-omega-ratio="1" aria-pressed="false">1:1</button>
                                    <button type="button" class="btn btn-outline-secondary" data-omega-action="aspect" data-omega-ratio="0.8" aria-pressed="false">4:5</button>
                                    <button type="button" class="btn btn-outline-secondary" data-omega-action="aspect" data-omega-ratio="{{ 16 / 9 }}" aria-pressed="false">16:9</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-omega-action="rotate-left"
                                        aria-label="{{ __('commerce.image_rotate_left') }}">
                                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-omega-action="rotate-right"
                                        aria-label="{{ __('commerce.image_rotate_right') }}">
                                    <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                                </button>
                                <span class="d-none d-inline-flex gap-2" data-omega-edit-actions>
                                    <button type="button" class="btn btn-sm btn-primary" data-omega-action="apply">
                                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i>{{ __('commerce.image_apply') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-omega-action="cancel-edit">
                                        {{ __('buttons.cancel') }}
                                    </button>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-omega-action="replace">
                                    <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>{{ __('commerce.image_replace') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-omega-action="remove">
                                    <i class="bi bi-trash me-1" aria-hidden="true"></i>{{ __('commerce.remove_image') }}
                                </button>
                            </div>
                        </div>

                        {{-- ── Pricing ─────────────────────────────── --}}
                        <h2 class="omega-form-section">{{ __('commerce.section_pricing') }}</h2>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('commerce.field_price') }} <span class="text-danger">*</span></label>
                            <div class="input-group" style="max-width:280px;">
                                <input type="number" id="price" name="price" required min="0" step="0.01"
                                       inputmode="decimal"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', $product?->price) }}">
                                <span class="input-group-text">{{ Auth::user()->merchant->currency ?? config('app.default_currency') }}</span>
                            </div>
                            <div class="form-text">{{ __('commerce.price_hint') }}</div>
                            @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        {{-- ── Inventory ───────────────────────────── --}}
                        <h2 class="omega-form-section">{{ __('commerce.section_inventory') }}</h2>

                        <div class="mb-3">
                            <label for="stock_qty" class="form-label">{{ __('commerce.field_stock') }}</label>
                            <input type="number" id="stock_qty" name="stock_qty" min="0" step="1"
                                   inputmode="numeric" style="max-width:280px;"
                                   class="form-control @error('stock_qty') is-invalid @enderror"
                                   value="{{ old('stock_qty', $product?->stock_qty) }}"
                                   placeholder="{{ __('commerce.stock_untracked') }}">
                            <div class="form-text">{{ __('commerce.stock_hint') }}</div>
                            @error('stock_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- ── Visibility ──────────────────────────── --}}
                        <h2 class="omega-form-section">{{ __('commerce.section_visibility') }}</h2>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label for="status" class="form-label">{{ __('commerce.col_status') }}</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="active" {{ old('status', $product?->status ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('commerce.status_active') }}</option>
                                    <option value="hidden" {{ old('status', $product?->status) === 'hidden' ? 'selected' : '' }}>{{ __('commerce.status_hidden') }}</option>
                                </select>
                                <div class="form-text">{{ __('commerce.status_hint') }}</div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="category_name" class="form-label">{{ __('commerce.field_category') }}</label>
                                <input type="text" id="category_name" name="category_name" maxlength="100"
                                       list="category-options"
                                       class="form-control @error('category_name') is-invalid @enderror"
                                       value="{{ old('category_name', $product?->category?->name) }}">
                                <datalist id="category-options">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->name }}">
                                    @endforeach
                                </datalist>
                                <div class="form-text">{{ __('commerce.category_hint') }}</div>
                                @error('category_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('buttons.save_changes') }}
                            </button>
                            <a href="{{ route('commerce.products.index') }}" class="btn btn-outline-secondary">{{ __('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/product-image.js')
</x-app-layout>
