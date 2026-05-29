<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends OpenCartModel
{
    protected $table = 'product';

    protected $primaryKey = 'product_id';

    protected $casts = [
        'price' => 'float',
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

    public function scopeActive($query)
    {
        return $query
            ->where('status', 1)
            ->where('date_available', '<=', now()->toDateString());
    }

    public function scopeForStore($query, ?int $storeId = null)
    {
        $storeId ??= config('opencart.store_id');

        return $query->whereHas('stores', fn ($q) => $q->where('store_id', $storeId));
    }

    public function scopeInCategory($query, int $categoryId)
    {
        return $query->whereHas('categories', fn ($q) => $q->where('category_id', $categoryId));
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
