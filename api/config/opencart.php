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

    'image_base_url' => rtrim(env('OPENCART_IMAGE_BASE_URL', 'http://localhost/ecomchempro/upload/image'), '/'),

    'placeholder_image' => env('OPENCART_PLACEHOLDER_IMAGE', ''),

];
