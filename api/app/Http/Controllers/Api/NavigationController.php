<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NavigationMenuService;
use Illuminate\Http\JsonResponse;

class NavigationController extends Controller
{
    public function productsMenu(NavigationMenuService $navigation): JsonResponse
    {
        return response()->json([
            'categories' => $navigation->productsMenu(),
        ]);
    }
}
