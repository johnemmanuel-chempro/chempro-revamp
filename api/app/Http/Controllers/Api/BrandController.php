<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->query('per_page', 50), 100);

        $brands = Manufacturer::query()
            ->forStore()
            ->orderBy('name')
            ->paginate($perPage);

        return ManufacturerResource::collection($brands);
    }

    public function show(int $id): ManufacturerResource
    {
        $brand = Manufacturer::query()
            ->forStore()
            ->where('manufacturer_id', $id)
            ->firstOrFail();

        return new ManufacturerResource($brand);
    }
}
