<?php

namespace App\Services;

use App\Facades\SettingsConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SeoUrlService
{
    /** @var array<string, string> query => keyword */
    protected array $keywords = [];

    public function isEnabled(): bool
    {
        return true;
        return (bool) SettingsConfig::get('config_seo_url');
    }

    public function keyword(string $query): ?string
    {
        if (! isset($this->keywords[$query])) {
            $this->preloadByQueries([$query]);
        }

        return $this->keywords[$query] ?? null;
    }

    /**
     * @param  list<string>  $queries
     */
    public function preloadByQueries(array $queries): void
    {
        $queries = array_values(array_diff(array_unique($queries), array_keys($this->keywords)));

        if ($queries === []) {
            return;
        }

        $rows = DB::table('seo_url')
            ->where('store_id', config('opencart.store_id'))
            ->where('language_id', config('opencart.language_id'))
            ->whereIn('query', $queries)
            ->get(['query', 'keyword']);

        foreach ($queries as $query) {
            $this->keywords[$query] = '';
        }

        foreach ($rows as $row) {
            if ($row->keyword !== '') {
                $this->keywords[$row->query] = $row->keyword;
            }
        }
    }

    public function productSlug(int $productId): ?string
    {
        $keyword = $this->keyword('product_id='.$productId);

        return $keyword !== '' ? $keyword : null;
    }

    public function categorySlug(int $categoryId): ?string
    {
        $keyword = $this->keyword('category_id='.$categoryId);

        return $keyword !== '' ? $keyword : null;
    }

    public function manufacturerSlug(int $manufacturerId): ?string
    {
        $keyword = $this->keyword('manufacturer_id='.$manufacturerId);

        return $keyword !== '' ? $keyword : null;
    }

    /**
     * Category breadcrumb slugs (root → leaf) for a category.
     *
     * @return list<string>
     */
    public function categoryPathSlugs(int $categoryId): array
    {
        $pathIds = DB::table('category_path')
            ->where('category_id', $categoryId)
            ->orderBy('level')
            ->pluck('path_id')
            ->all();

        if ($pathIds === []) {
            $slug = $this->categorySlug($categoryId);

            return $slug ? [$slug] : [];
        }

        $this->preloadByQueries(array_map(fn ($id) => 'category_id='.$id, $pathIds));

        $slugs = [];

        foreach ($pathIds as $pathId) {
            $slug = $this->categorySlug((int) $pathId);

            if ($slug) {
                $slugs[] = $slug;
            }
        }

        return $slugs;
    }

    public function categoryPathString(int $categoryId): ?string
    {
        $slugs = $this->categoryPathSlugs($categoryId);

        return $slugs !== [] ? implode('/', $slugs) : null;
    }

    /**
     * Primary category for a product (deepest category path), used for nested product URLs.
     */
    public function primaryCategoryIdForProduct(int $productId): ?int
    {
        $categoryId = DB::table('product_to_category as p2c')
            ->join('category_path as cp', 'p2c.category_id', '=', 'cp.category_id')
            ->where('p2c.product_id', $productId)
            ->orderByDesc('cp.level')
            ->value('p2c.category_id');

        if ($categoryId) {
            return (int) $categoryId;
        }

        $fallback = DB::table('product_to_category')
            ->where('product_id', $productId)
            ->orderBy('category_id')
            ->value('category_id');

        return $fallback ? (int) $fallback : null;
    }

    /**
     * Product URL path segments (category slugs + product slug) when SEO is enabled.
     *
     * @return list<string>
     */
    public function productPathSlugs(int $productId, bool $includeCategoryPath = true): array
    {
        $segments = [];

        if ($includeCategoryPath && config('opencart.seo_product_include_category_path', true)) {
            $categoryId = $this->primaryCategoryIdForProduct($productId);

            if ($categoryId) {
                $segments = $this->categoryPathSlugs($categoryId);
            }
        }

        $productSlug = $this->productSlug($productId);

        if ($productSlug) {
            $segments[] = $productSlug;
        }

        return $segments;
    }

    public function productPath(int $productId, bool $includeCategoryPath = true): ?string
    {
        $segments = $this->productPathSlugs($productId, $includeCategoryPath);

        return $segments !== [] ? implode('/', $segments) : null;
    }

    public function productUrl(int $productId, bool $includeCategoryPath = true): ?string
    {
        if ($this->isEnabled()) {
            $path = $this->productPath($productId, $includeCategoryPath);

            return $path ? $this->absolute($path) : null;
        }

        return $this->absolute('index.php', [
            'route' => 'product/product',
            'product_id' => $productId,
        ]);
    }

    public function categoryUrl(int $categoryId): ?string
    {
        if ($this->isEnabled()) {
            $path = $this->categoryPathString($categoryId);

            return $path ? $this->absolute($path) : null;
        }

        return $this->absolute('index.php', [
            'route' => 'product/category',
            'path' => $categoryId,
        ]);
    }

    public function manufacturerUrl(int $manufacturerId): ?string
    {
        if ($this->isEnabled()) {
            $prefix = $this->manufacturerRoutePrefix();
            $slug = $this->manufacturerSlug($manufacturerId);

            if (! $slug) {
                return null;
            }

            $path = $prefix ? trim($prefix, '/').'/'.$slug : $slug;

            return $this->absolute($path);
        }

        return $this->absolute('index.php', [
            'route' => 'product/manufacturer/info',
            'manufacturer_id' => $manufacturerId,
        ]);
    }

    /**
     * Batch-load SEO data for a product list page.
     */
    public function preloadProducts(Collection $products, bool $includeCategoryPath = true): void
    {
        $queries = [];

        foreach ($products as $product) {
            $queries[] = 'product_id='.$product->product_id;

            if ($includeCategoryPath) {
                $categoryId = $this->primaryCategoryIdForProduct($product->product_id);

                if ($categoryId) {
                    $pathIds = DB::table('category_path')
                        ->where('category_id', $categoryId)
                        ->pluck('path_id');

                    foreach ($pathIds as $pathId) {
                        $queries[] = 'category_id='.$pathId;
                    }
                }
            }
        }

        $this->preloadByQueries($queries);
    }

    public function preloadCategories(Collection $categories): void
    {
        $queries = [];

        foreach ($categories as $category) {
            $pathIds = DB::table('category_path')
                ->where('category_id', $category->category_id)
                ->pluck('path_id');

            foreach ($pathIds as $pathId) {
                $queries[] = 'category_id='.$pathId;
            }

            $queries[] = 'category_id='.$category->category_id;
        }

        $this->preloadByQueries($queries);
    }

    public function preloadManufacturers(Collection $manufacturers): void
    {
        $queries = ['route=product/manufacturer'];

        foreach ($manufacturers as $manufacturer) {
            $queries[] = 'manufacturer_id='.$manufacturer->manufacturer_id;
        }

        $this->preloadByQueries($queries);
    }

    protected function manufacturerRoutePrefix(): string
    {
        $keyword = $this->keyword('route=product/manufacturer');

        return $keyword !== '' ? '/'.$keyword : '';
    }

    protected function storefrontBase(): string
    {
        if ($base = config('opencart.storefront_url')) {
            return rtrim($base, '/');
        }

        $url = SettingsConfig::get('config_ssl') ?: SettingsConfig::get('config_url');

        return rtrim((string) $url, '/');
    }

  /**
     * @param  array<string, int|string>  $query
     */
    protected function absolute(string $path, array $query = []): string
    {
        $base = $this->storefrontBase();
        $path = ltrim($path, '/');

        if ($query === []) {
            return $path === '' ? $base.'/' : $base.'/'.$path;
        }

        return $base.'/'.($path !== '' ? $path : 'index.php').'?'.http_build_query($query);
    }
}
