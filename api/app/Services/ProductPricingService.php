<?php

namespace App\Services;

use App\Models\Product;

class ProductPricingService
{
    /**
     * Resolve storefront pricing (matches OpenCart + Chempro RRP / save_amount logic).
     *
     * @return array{
     *     base_price: float,
     *     regular_price: float,
     *     special_price: float|null,
     *     rrp_price: float|null,
     *     old_price: float|null,
     *     current_price: float,
     *     save_amount: float|null,
     *     on_sale: bool
     * }
     */
    public function forProduct(Product $product): array
    {
        $basePrice = (float) $product->price;
        $rrpPrice = (float) ($product->rrp_price ?? 0);

        $regularPrice = $this->attributeFloat($product, 'catalog_discount_price') ?? $basePrice;

        $specialRaw = $product->getAttribute('catalog_special_price');
        $hasSpecial = $specialRaw !== null && $specialRaw !== '' && (float) $specialRaw >= 0;
        $specialPrice = $hasSpecial ? (float) $specialRaw : null;

        $oldPrice = null;
        $currentPrice = $regularPrice;
        $saveAmount = null;
        $onSale = false;

        if ($hasSpecial) {
            $onSale = true;
            $currentPrice = $specialPrice;

            if ($rrpPrice > $regularPrice) {
                $oldPrice = $rrpPrice;
                $saveAmount = max(0, $rrpPrice - $specialPrice);
            } else {
                $oldPrice = $regularPrice;
                $saveAmount = max(0, $regularPrice - $specialPrice);
            }
        } elseif ($rrpPrice > $regularPrice) {
            $onSale = true;
            $oldPrice = $rrpPrice;
            $currentPrice = $regularPrice;
            $saveAmount = $rrpPrice - $regularPrice;
        }

        return [
            'base_price' => $this->roundMoney($basePrice),
            'regular_price' => $this->roundMoney($regularPrice),
            'special_price' => $specialPrice !== null ? $this->roundMoney($specialPrice) : null,
            'rrp_price' => $rrpPrice > 0 ? $this->roundMoney($rrpPrice) : null,
            'old_price' => $oldPrice !== null ? $this->roundMoney($oldPrice) : null,
            'current_price' => $this->roundMoney($currentPrice),
            'save_amount' => $saveAmount !== null ? $this->roundMoney($saveAmount) : null,
            'on_sale' => $onSale,
        ];
    }

    protected function attributeFloat(Product $product, string $key): ?float
    {
        if (! $product->offsetExists($key)) {
            return null;
        }

        $value = $product->getAttribute($key);

        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    protected function roundMoney(float $amount): float
    {
        return round($amount, 2);
    }
}
