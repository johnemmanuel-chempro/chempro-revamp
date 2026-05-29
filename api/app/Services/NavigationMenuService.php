<?php

namespace App\Services;

use App\Facades\SeoUrl;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NavigationMenuService
{
    public function productsMenu(): array
    {
        $storeId = (int) config('opencart.store_id');
        $ttl = (int) config('opencart.navigation_cache_ttl', 3600);

        return Cache::store(config('opencart.settings_cache_store', 'file'))->remember(
            'opencart.navigation.products_menu.'.$storeId,
            $ttl,
            fn () => $this->buildProductsMenu()
        );
    }

    protected function buildProductsMenu(): array
    {
        $maxDepth = (int) config('opencart.navigation_max_depth', 3);
        $productsPerCategory = (int) config('opencart.navigation_products_per_category', 8);

        $categories = Category::query()
            ->with('description')
            ->active()
            ->forStore()
            ->orderBy('sort_order')
            ->orderBy('category_id')
            ->get();

        SeoUrl::preloadCategories($categories);

        $byParent = $categories->groupBy(fn (Category $c) => (int) $c->parent_id);

        $tree = $this->buildCategoryBranch($byParent, 0, 0, $maxDepth);

        $leafIds = $this->collectLeafCategoryIds($tree);

        $productsByCategory = $this->loadProductsForCategories($leafIds, $productsPerCategory);

        $tree = $this->attachProducts($tree, $productsByCategory);

        return $tree;
    }

    /**
     * @param  Collection<int, Collection<int, Category>>  $byParent
     * @return list<array<string, mixed>>
     */
    protected function buildCategoryBranch(Collection $byParent, int $parentId, int $depth, int $maxDepth): array
    {
        $nodes = [];

        foreach ($byParent->get($parentId, collect()) as $category) {
            $children = $depth + 1 < $maxDepth
                ? $this->buildCategoryBranch($byParent, (int) $category->category_id, $depth + 1, $maxDepth)
                : [];

            $nodes[] = $this->categoryNode($category, $children);
        }

        return $nodes;
    }

    /**
     * @param  list<array<string, mixed>>  $branch
     * @return list<int>
     */
    protected function collectLeafCategoryIds(array $branch): array
    {
        $ids = [];

        foreach ($branch as $node) {
            if ($node['children'] === []) {
                $ids[] = $node['id'];
            } else {
                $ids = array_merge($ids, $this->collectLeafCategoryIds($node['children']));
            }
        }

        return $ids;
    }

    /**
     * @param  list<int>  $categoryIds
     * @return array<int, list<array<string, mixed>>>
     */
    protected function loadProductsForCategories(array $categoryIds, int $limit): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $grouped = [];

        foreach ($categoryIds as $categoryId) {
            $products = Product::query()
                ->withCatalogPricing()
                ->with('description')
                ->active()
                ->forStore()
                ->inCategory($categoryId)
                ->orderBy('sort_order')
                ->orderBy('product_id')
                ->limit($limit)
                ->get();

            if ($products->isEmpty()) {
                continue;
            }

            SeoUrl::preloadProducts($products);

            $grouped[$categoryId] = $products->map(fn (Product $product) => $this->productNode($product))->all();
        }

        return $grouped;
    }

    /**
     * @param  list<array<string, mixed>>  $branch
     * @param  array<int, list<array<string, mixed>>>  $productsByCategory
     * @return list<array<string, mixed>>
     */
    protected function attachProducts(array $branch, array $productsByCategory): array
    {
        foreach ($branch as &$node) {
            if ($node['children'] === []) {
                $node['products'] = $productsByCategory[$node['id']] ?? [];
            } else {
                $node['children'] = $this->attachProducts($node['children'], $productsByCategory);
                $node['products'] = [];
            }
        }

        return $branch;
    }

    /**
     * @param  list<array<string, mixed>>  $children
     * @return array<string, mixed>
     */
    protected function categoryNode(Category $category, array $children): array
    {
        $path = SeoUrl::categoryPathString((int) $category->category_id);
        $slug = SeoUrl::categorySlug((int) $category->category_id);

        return [
            'id' => (int) $category->category_id,
            'name' => $category->description?->name,
            'slug' => $slug,
            'path' => $path,
            'url' => $path ? '/'.$path : null,
            'children' => $children,
            'products' => [],
        ];
    }

    protected function productNode(Product $product): array
    {
        $path = SeoUrl::productPath((int) $product->product_id, false);
        $pricing = app(ProductPricingService::class)->forProduct($product);

        return [
            'id' => (int) $product->product_id,
            'name' => $product->description?->name,
            'slug' => SeoUrl::productSlug((int) $product->product_id),
            'path' => $path,
            'url' => $path ? '/'.$path : null,
            'price' => $pricing['current_price'],
        ];
    }
}
