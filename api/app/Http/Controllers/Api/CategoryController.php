<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $parentId = (int) $request->query('parent_id', 0);
        $perPage = min((int) $request->query('per_page', 50), 100);

        $categories = Category::query()
            ->with('description')
            ->active()
            ->forStore()
            ->where('parent_id', $parentId)
            ->orderBy('sort_order')
            ->orderBy('category_id')
            ->paginate($perPage);

        return CategoryResource::collection($categories);
    }

    public function show(int $id): CategoryResource
    {
        $category = Category::query()
            ->with('description')
            ->active()
            ->forStore()
            ->where('category_id', $id)
            ->firstOrFail();

        return new CategoryResource($category);
    }
}
