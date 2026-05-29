<?php

namespace App\Http\Resources\Concerns;

use App\Models\Product;
use App\Services\ProductPricingService;

trait HasProductPricing
{
  /**
     * @return array<string, mixed>
     */
    protected function productPricingFields(Product $product): array
    {
        $pricing = app(ProductPricingService::class)->forProduct($product);

        return [
            'price' => $pricing['current_price'],
            'pricing' => $pricing,
        ];
    }
}
