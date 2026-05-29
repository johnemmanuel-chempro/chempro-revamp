<?php

namespace App\Http\Resources;

use App\Support\OpenCartImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->product_id,
            'name' => $this->whenLoaded('description', fn () => $this->description?->name),
            'description' => $this->whenLoaded('description', fn () => $this->description?->description),
            'meta_title' => $this->whenLoaded('description', fn () => $this->description?->meta_title),
            'model' => $this->model,
            'sku' => $this->sku,
            'upc' => $this->upc,
            'ean' => $this->ean,
            'price' => $this->price,
            'quantity' => (int) $this->quantity,
            'minimum' => (int) $this->minimum,
            'image' => OpenCartImage::url($this->image),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->product_image_id,
                'image' => OpenCartImage::url($img->image),
                'sort_order' => (int) $img->sort_order,
            ])),
            'manufacturer' => $this->whenLoaded('manufacturer', fn () => $this->manufacturer ? [
                'id' => $this->manufacturer->manufacturer_id,
                'name' => $this->manufacturer->name,
                'image' => OpenCartImage::url($this->manufacturer->image),
            ] : null),
            'categories' => $this->whenLoaded('categories', fn () => $this->categories->map(fn ($cat) => [
                'id' => $cat->category_id,
                'name' => $cat->description?->name,
            ])),
        ];
    }
}
