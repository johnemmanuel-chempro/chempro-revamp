<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenCart integration
    |--------------------------------------------------------------------------
    |
    | Align with upload/config.php (store, language, image path, table prefix).
    |
    */

    'store_id' => (int) env('OPENCART_STORE_ID', 0),

    'language_id' => (int) env('OPENCART_LANGUAGE_ID', 1),

    'customer_group_id' => (int) env('OPENCART_CUSTOMER_GROUP_ID', 1),

    'image_base_url' => rtrim(env('OPENCART_IMAGE_BASE_URL', 'http://localhost/ecomchempro/upload/image'), '/'),

    /*
    | Storefront base URL for SEO links (no trailing slash).
    | Falls back to config_ssl / config_url from oc_setting when empty.
    */
    'storefront_url' => env('OPENCART_STOREFRONT_URL'),

    /*
    | Product URLs: include category path segments before product slug (OpenCart-style).
    */
    'seo_product_include_category_path' => env('OPENCART_SEO_PRODUCT_CATEGORY_PATH', true),

    'placeholder_image' => env('OPENCART_PLACEHOLDER_IMAGE', ''),

    /*
    | Seconds to cache oc_setting rows per store (0 = forever until clearCache).
    */
    'settings_cache_ttl' => (int) env('OPENCART_SETTINGS_CACHE_TTL', 3600),

    'settings_cache_store' => env('OPENCART_SETTINGS_CACHE_STORE', 'file'),

    'navigation_max_depth' => (int) env('OPENCART_NAVIGATION_MAX_DEPTH', 3),

    'navigation_products_per_category' => (int) env('OPENCART_NAVIGATION_PRODUCTS_PER_CATEGORY', 8),

    'navigation_cache_ttl' => (int) env('OPENCART_NAVIGATION_CACHE_TTL', 3600),

];
