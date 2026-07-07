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

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('commerce.field_name') }} <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" required maxlength="150"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product?->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('commerce.field_description') }}</label>
                            <textarea id="description" name="description" rows="3" maxlength="1000"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $product?->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Main product image (OMEGA-001A: one image; gallery = future) --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('commerce.field_image') }}</label>
                            <x-ui.media-upload
                                name="image"
                                remove-name="remove_image"
                                :current-url="$product?->imageUrl()"
                                :current-label="$product?->name" />
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="price" class="form-label">{{ __('commerce.field_price') }} <span class="text-danger">*</span></label>
                                <input type="number" id="price" name="price" required min="0" step="0.01"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', $product?->price) }}">
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label for="stock_qty" class="form-label">{{ __('commerce.field_stock') }}</label>
                                <input type="number" id="stock_qty" name="stock_qty" min="0" step="1"
                                       class="form-control @error('stock_qty') is-invalid @enderror"
                                       value="{{ old('stock_qty', $product?->stock_qty) }}"
                                       placeholder="{{ __('commerce.stock_untracked') }}">
                                @error('stock_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">{{ __('commerce.stock_hint') }}</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
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
                                @error('category_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label for="status" class="form-label">{{ __('commerce.col_status') }}</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="active" {{ old('status', $product?->status ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('commerce.status_active') }}</option>
                                    <option value="hidden" {{ old('status', $product?->status) === 'hidden' ? 'selected' : '' }}>{{ __('commerce.status_hidden') }}</option>
                                </select>
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
</x-app-layout>
