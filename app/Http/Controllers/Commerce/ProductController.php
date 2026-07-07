<?php

namespace App\Http\Controllers\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** APP-001 — Commerce App: merchant catalogue CRUD (gated app.installed:commerce). */
class ProductController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        app(\App\Services\LaunchChecklistService::class)->markFlag($merchant, 'storefront_reviewed');

        $products = Product::where('merchant_id', $merchant->id)
            ->with('category')
            ->orderBy('name')
            ->paginate(25);

        return view('commerce.products.index', compact('products', 'merchant'));
    }

    public function create(Request $request)
    {
        return view('commerce.products.form', [
            'product'    => null,
            'categories' => $this->categories($request),
        ]);
    }

    public function store(Request $request)
    {
        $merchant  = $request->user()->merchant;
        $validated = $this->validated($request);

        // Free-typed category name → find-or-create for this merchant
        $validated['product_category_id'] = $this->resolveCategory($request, $merchant->id);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->media->store($request->file('image'), 'products', $merchant->id);
        }

        Product::create(array_merge($validated, ['merchant_id' => $merchant->id]));

        return redirect()->route('commerce.products.index')
            ->with('success', __('commerce.product_created'));
    }

    public function edit(Request $request, Product $product)
    {
        abort_unless($product->merchant_id === $request->user()->merchant?->id, 403);

        return view('commerce.products.form', [
            'product'    => $product,
            'categories' => $this->categories($request),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $this->validated($request);
        $validated['product_category_id'] = $this->resolveCategory($request, $product->merchant_id);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->media->replace(
                $product->image_path, $request->file('image'), 'products', $product->merchant_id,
            );
        } elseif ($request->boolean('remove_image')) {
            $this->media->delete($product->image_path);
            $validated['image_path'] = null;
        }

        $product->update($validated);

        return redirect()->route('commerce.products.index')
            ->with('success', __('commerce.product_updated'));
    }

    public function archive(Request $request, Product $product)
    {
        abort_unless($product->merchant_id === $request->user()->merchant?->id, 403);

        $product->delete();

        return redirect()->route('commerce.products.index')
            ->with('success', __('commerce.product_archived'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image'       => $this->media->validationRules('products'),
            'price'       => ['required', 'numeric', 'min:0', 'max:9999999'],
            'stock_qty'   => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'status'      => ['required', Rule::in(['active', 'hidden'])],
        ]);
    }

    private function resolveCategory(Request $request, int $merchantId): ?int
    {
        $name = trim((string) $request->input('category_name', ''));
        if ($name === '') {
            return null;
        }

        $request->validate(['category_name' => ['string', 'max:100']]);

        return ProductCategory::firstOrCreate(
            ['merchant_id' => $merchantId, 'name' => $name],
        )->id;
    }

    private function categories(Request $request)
    {
        return ProductCategory::where('merchant_id', $request->user()->merchant->id)
            ->orderBy('sort_order')->orderBy('name')->get();
    }
}
