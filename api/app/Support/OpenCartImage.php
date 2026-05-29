<?php

namespace App\Support;

class OpenCartImage
{
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $base = config('opencart.image_base_url');

        return $base.'/'.ltrim($path, '/');
    }
}
