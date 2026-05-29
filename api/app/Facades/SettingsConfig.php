<?php

namespace App\Facades;

use App\Services\SettingsConfigService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool has(string $key)
 * @method static array all(?int $storeId = null)
 * @method static array code(string $code, ?int $storeId = null)
 * @method static void clearCache(?int $storeId = null)
 *
 * @see SettingsConfigService
 */
class SettingsConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsConfigService::class;
    }
}
