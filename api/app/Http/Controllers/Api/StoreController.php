<?php

namespace App\Http\Controllers\Api;

use App\Facades\SettingsConfig;
use App\Http\Controllers\Controller;
use App\Support\OpenCartImage;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'name' => SettingsConfig::get('config_name'),
            'logo' => OpenCartImage::url(SettingsConfig::get('config_logo')),
            'email' => SettingsConfig::get('config_email'),
            'telephone' => SettingsConfig::get('config_telephone'),
            'meta' => [
                'title' => SettingsConfig::get('config_meta_title'),
                'description' => SettingsConfig::get('config_meta_description'),
                'keywords' => SettingsConfig::get('config_meta_keyword'),
            ],
        ]);
    }
}
