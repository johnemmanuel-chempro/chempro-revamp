<?php

namespace App\Http\Controllers\Api;

use App\Facades\SeoUrl;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->query('per_page', 20), 100);

        $query = Product::query()
            ->withCatalogPricing()
            ->with(['description', 'manufacturer'])
            ->active()
            ->forStore();

        if ($request->filled('category_id')) {
            $query->inCategory((int) $request->query('category_id'));
        }

        if ($request->filled('brand_id')) {
            $query->where('manufacturer_id', (int) $request->query('brand_id'));
        }

        if ($request->filled('search')) {
            $query->search($request->query('search'));
        }

        $products = $query
            ->orderBy('sort_order')
            ->orderBy('product_id')
            ->paginate($perPage);

        SeoUrl::preloadProducts($products->getCollection());

        $manufacturers = $products->getCollection()->pluck('manufacturer')->filter();

        if ($manufacturers->isNotEmpty()) {
            SeoUrl::preloadManufacturers($manufacturers);
        }

        return ProductResource::collection($products);
    }

    public function show(int $id): ProductDetailResource
    {
        $product = Product::query()
            ->withCatalogPricing()
            ->with([
                'description',
                'manufacturer',
                'categories.description',
                'images',
            ])
            ->active()
            ->forStore()
            ->where('product_id', $id)
            ->firstOrFail();

        SeoUrl::preloadProducts(collect([$product]));

        if ($product->manufacturer) {
            SeoUrl::preloadManufacturers(collect([$product->manufacturer]));
        }

        SeoUrl::preloadCategories($product->categories);

        return new ProductDetailResource($product);
    }
}
