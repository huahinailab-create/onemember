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

                        {{-- OMEGA-001A/C — reusable premium media upload (one main image; gallery = future) --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('commerce.field_image') }}</label>
                            <x-ui.media-upload
                                name="image"
                                remove-name="remove_image"
                                :current-url="$product?->imageUrl()"
                                :current-label="$product?->name" />
                            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
