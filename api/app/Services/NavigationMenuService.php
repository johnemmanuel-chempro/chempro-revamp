<?php

namespace App\Services;

use App\Facades\SeoUrl;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NavigationMenuService
{
    public function productsMenu(): array
    {
        $storeId = (int) config('opencart.store_id');
        $ttl = (int) config('opencart.navigation_cache_ttl', 3600);

        return Cache::store(config('opencart.settings_cache_store', 'file'))->remember(
            'opencart.navigation.categories_menu.v2.'.$storeId,
            $ttl,
            fn () => $this->buildProductsMenu()
        );
    }

    protected function buildProductsMenu(): array
    {
        $maxDepth = (int) config('opencart.navigation_max_depth', 3);

        $categories = Category::query()
            ->with('description')
            ->active()
            ->forStore()
            ->orderBy('sort_order')
            ->orderBy('category_id')
            ->get();

        SeoUrl::preloadCategories($categories);

        $byParent = $categories->groupBy(fn (Category $c) => (int) $c->parent_id);

        return $this->buildCategoryBranch($byParent, 0, 0, $maxDepth);
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

    protected function decodeText(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
            'name' => $this->decodeText($category->description?->name),
            'slug' => $slug,
            'path' => $path,
            'url' => $path ? '/'.$path : null,
            'children' => $children,
        ];
    }
}
