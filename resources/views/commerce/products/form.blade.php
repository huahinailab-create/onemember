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

                        {{-- Main product image (BETA-008A: one image; gallery = future) --}}
                        <div class="mb-3">
                            <label for="image" class="form-label">{{ __('commerce.field_image') }}</label>
                            <div class="d-flex align-items-start gap-3 flex-wrap">
                                <div class="commerce-product-thumb commerce-product-thumb-lg flex-shrink-0">
                                    <img id="image-preview"
                                         src="{{ $product?->imageUrl() ?? '' }}"
                                         alt="{{ $product?->name ? __('commerce.field_image') . ': ' . $product->name : __('commerce.field_image') }}"
                                         class="{{ $product?->imageUrl() ? '' : 'd-none' }}">
                                    <i id="image-placeholder" class="bi bi-image text-muted {{ $product?->imageUrl() ? 'd-none' : '' }}" aria-hidden="true"></i>
                                </div>
                                <div class="flex-grow-1" style="min-width:220px;">
                                    <input type="file" id="image" name="image"
                                           accept="image/jpeg,image/png,image/webp"
                                           class="form-control @error('image') is-invalid @enderror">
                                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">{{ __('commerce.image_hint') }}</div>
                                    @if ($product?->imageUrl())
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label" for="remove_image">{{ __('commerce.remove_image') }}</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
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

    <script>
        // Live preview of the selected image (client-side only; server re-validates).
        document.getElementById('image')?.addEventListener('change', function () {
            const file = this.files && this.files[0];
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            if (file && file.type.startsWith('image/')) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
                placeholder?.classList.add('d-none');
                const remove = document.getElementById('remove_image');
                if (remove) remove.checked = false;
            }
        });
    </script>
</x-app-layout>
