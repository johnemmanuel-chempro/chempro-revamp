<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasProductPricing;
use App\Http\Resources\Concerns\HasSeoUrls;
use App\Support\OpenCartImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    use HasProductPricing;
    use HasSeoUrls;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->product_id,
            'name' => $this->whenLoaded('description', fn () => $this->description?->name),
            'model' => $this->model,
            'sku' => $this->sku,
            ...$this->productPricingFields($this->resource),
            'quantity' => (int) $this->quantity,
            'image' => OpenCartImage::url($this->image),
            ...$this->productSeoFields((int) $this->product_id),
            'manufacturer' => $this->whenLoaded('manufacturer', fn () => $this->manufacturer ? [
                'id' => $this->manufacturer->manufacturer_id,
                'name' => $this->manufacturer->name,
                ...$this->manufacturerSeoFields((int) $this->manufacturer->manufacturer_id),
            ] : null),
        ];
    }
}
