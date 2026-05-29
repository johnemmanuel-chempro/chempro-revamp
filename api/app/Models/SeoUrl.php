<?php

namespace App\Models;

class SeoUrl extends OpenCartModel
{
    protected $table = 'seo_url';

    protected $primaryKey = 'seo_url_id';

    protected $casts = [
        'store_id' => 'integer',
        'language_id' => 'integer',
    ];
}
