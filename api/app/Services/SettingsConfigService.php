<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsConfigService
{
    protected ?array $settings = null;

    protected ?int $storeId = null;

    protected function cache(): CacheRepository
    {
        return Cache::store(config('opencart.settings_cache_store', 'file'));
    }

    /**
     * Get a setting value (same keys as OpenCart config->get()).
     *
     * @example SettingsConfig::get('config_email')
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    /**
     * All settings for the active store (store 0 defaults, then store overrides).
     */
    public function all(?int $storeId = null): array
    {
        $storeId = $this->resolveStoreId($storeId);

        if ($this->settings !== null && $this->storeId === $storeId) {
            return $this->settings;
        }

        $this->storeId = $storeId;
        $this->settings = $this->cache()->remember(
            $this->cacheKey($storeId),
            config('opencart.settings_cache_ttl'),
            fn () => $this->loadFromDatabase($storeId)
        );

        return $this->settings;
    }

    /**
     * Settings grouped by OpenCart "code" column (e.g. code = config).
     */
    public function code(string $code, ?int $storeId = null): array
    {
        $storeId = $this->resolveStoreId($storeId);

        return $this->cache()->remember(
            $this->cacheKey($storeId).'.code.'.$code,
            config('opencart.settings_cache_ttl'),
            fn () => $this->loadCodeFromDatabase($code, $storeId)
        );
    }

    public function clearCache(?int $storeId = null): void
    {
        $storeId = $this->resolveStoreId($storeId);

        $this->cache()->forget($this->cacheKey($storeId));

        $this->settings = null;
        $this->storeId = null;
    }

    protected function resolveStoreId(?int $storeId): int
    {
        return $storeId ?? (int) config('opencart.store_id');
    }

    protected function cacheKey(int $storeId): string
    {
        return 'opencart.settings.store.'.$storeId;
    }

    /**
     * Mirrors OpenCart catalog/controller/startup/startup.php settings load.
     */
    protected function loadFromDatabase(int $storeId): array
    {
        $rows = DB::table('setting')
            ->where(fn ($q) => $q->where('store_id', 0)->orWhere('store_id', $storeId))
            ->orderBy('store_id')
            ->get(['key', 'value', 'serialized']);

        $settings = [];

        foreach ($rows as $row) {
            $settings[$row->key] = $this->parseValue($row->value, (bool) $row->serialized);
        }

        return $settings;
    }

    protected function loadCodeFromDatabase(string $code, int $storeId): array
    {
        $rows = DB::table('setting')
            ->where('code', $code)
            ->where(fn ($q) => $q->where('store_id', 0)->orWhere('store_id', $storeId))
            ->orderBy('store_id')
            ->get(['key', 'value', 'serialized']);

        $settings = [];

        foreach ($rows as $row) {
            $settings[$row->key] = $this->parseValue($row->value, (bool) $row->serialized);
        }

        return $settings;
    }

    protected function parseValue(string $value, bool $serialized): mixed
    {
        if (! $serialized) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
}
