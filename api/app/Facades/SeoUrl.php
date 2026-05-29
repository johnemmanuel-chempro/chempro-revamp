<?php

namespace App\Facades;

use App\Services\SeoUrlService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEnabled()
 * @method static string|null keyword(string $query)
 * @method static void preloadByQueries(array $queries)
 * @method static string|null productSlug(int $productId)
 * @method static string|null categorySlug(int $categoryId)
 * @method static string|null manufacturerSlug(int $manufacturerId)
 * @method static array categoryPathSlugs(int $categoryId)
 * @method static string|null categoryPathString(int $categoryId)
 * @method static string|null productPath(int $productId, bool $includeCategoryPath = true)
 * @method static string|null productUrl(int $productId, bool $includeCategoryPath = true)
 * @method static string|null categoryUrl(int $categoryId)
 * @method static string|null manufacturerUrl(int $manufacturerId)
 * @method static void preloadProducts(\Illuminate\Support\Collection $products, bool $includeCategoryPath = true)
 * @method static void preloadCategories(\Illuminate\Support\Collection $categories)
 * @method static void preloadManufacturers(\Illuminate\Support\Collection $manufacturers)
 *
 * @see SeoUrlService
 */
class SeoUrl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SeoUrlService::class;
    }
}
