<?php

namespace App\Http\Resources\Concerns;

use App\Facades\SeoUrl;

trait HasSeoUrls
{
    /**
     * @return array{slug: string|null, path: string|null, url: string|null}
     */
    protected function productSeoFields(int $productId): array
    {
        return [
            'slug' => SeoUrl::productSlug($productId),
            'path' => SeoUrl::productPath($productId),
            'url' => SeoUrl::productUrl($productId),
        ];
    }

    /**
     * @return array{slug: string|null, path: string|null, url: string|null}
     */
    protected function categorySeoFields(int $categoryId): array
    {
        return [
            'slug' => SeoUrl::categorySlug($categoryId),
            'path' => SeoUrl::categoryPathString($categoryId),
            'url' => SeoUrl::categoryUrl($categoryId),
        ];
    }

    /**
     * @return array{slug: string|null, url: string|null}
     */
    protected function manufacturerSeoFields(int $manufacturerId): array
    {
        return [
            'slug' => SeoUrl::manufacturerSlug($manufacturerId),
            'url' => SeoUrl::manufacturerUrl($manufacturerId),
        ];
    }
}
