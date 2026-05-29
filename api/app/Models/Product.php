<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends OpenCartModel
{
    protected $table = 'product';

    protected $primaryKey = 'product_id';

    protected $casts = [
        'price' => 'float',
        'rrp_price' => 'float',
        'quantity' => 'integer',
        'status' => 'boolean',
        'date_available' => 'date',
    ];

    public function description(): HasOne
    {
        return $this->hasOne(ProductDescription::class, 'product_id', 'product_id')
            ->where('language_id', config('opencart.language_id'));
    }

    public function descriptions(): HasMany
    {
        return $this->hasMany(ProductDescription::class, 'product_id', 'product_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id', 'manufacturer_id');
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(
            Store::class,
            'product_to_store',
            'product_id',
            'store_id'
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'product_to_category',
            'product_id',
            'category_id'
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')
            ->orderBy('sort_order');
    }

    public function scopeWithCatalogPricing(Builder $query): Builder
    {
        $customerGroupId = (int) config('opencart.customer_group_id');

        $discountSubquery = DB::table('product_discount as pd2')
            ->select('pd2.price')
            ->whereColumn('pd2.product_id', 'product.product_id')
            ->where('pd2.customer_group_id', $customerGroupId)
            ->where('pd2.quantity', 1)
            ->where(function ($q) {
                $q->where('pd2.date_start', '0000-00-00')
                    ->orWhere('pd2.date_start', '<', now());
            })
            ->where(function ($q) {
                $q->where('pd2.date_end', '0000-00-00')
                    ->orWhere('pd2.date_end', '>', now());
            })
            ->orderBy('pd2.priority')
            ->orderBy('pd2.price')
            ->limit(1);

        $specialSubquery = DB::table('product_special as ps')
            ->select('ps.price')
            ->whereColumn('ps.product_id', 'product.product_id')
            ->where('ps.customer_group_id', $customerGroupId)
            ->where(function ($q) {
                $q->where('ps.date_start', '0000-00-00')
                    ->orWhere('ps.date_start', '<', now());
            })
            ->where(function ($q) {
                $q->where('ps.date_end', '0000-00-00')
                    ->orWhere('ps.date_end', '>', now());
            })
            ->orderBy('ps.priority')
            ->orderBy('ps.price')
            ->limit(1);

        return $query->addSelect([
            'product.*',
            'catalog_discount_price' => $discountSubquery,
            'catalog_special_price' => $specialSubquery,
        ]);
    }

    public function scopeActive($query)
    {
        return $query
            ->where('status', 1)
            ->where('date_available', '<=', now()->toDateString());
    }

    public function scopeForStore($query, ?int $storeId = null)
    {
        $storeId ??= config('opencart.store_id');

        return $query->whereExists(function ($sub) use ($storeId) {
            $sub->from('product_to_store')
                ->whereColumn('product_to_store.product_id', 'product.product_id')
                ->where('product_to_store.store_id', $storeId);
        });
    }

    public function scopeInCategory($query, int $categoryId)
    {
        return $query->whereHas('categories', fn ($q) => $q->where('product_to_category.category_id', $categoryId));
    }

    public function scopeSearch($query, string $term)
    {
        $term = '%'.$term.'%';
        $languageId = config('opencart.language_id');

        return $query->where(function ($q) use ($term, $languageId) {
            $q->whereHas('description', fn ($d) => $d
                ->where('language_id', $languageId)
                ->where(fn ($d) => $d->where('name', 'like', $term)->orWhere('tag', 'like', $term))
            )->orWhere('model', 'like', $term)
                ->orWhere('sku', 'like', $term);
        });
    }
}
