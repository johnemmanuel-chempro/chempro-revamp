<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturer extends OpenCartModel
{
    protected $table = 'manufacturer';

    protected $primaryKey = 'manufacturer_id';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'manufacturer_id', 'manufacturer_id');
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(
            Store::class,
            'manufacturer_to_store',
            'manufacturer_id',
            'store_id'
        );
    }

    public function scopeForStore($query, ?int $storeId = null)
    {
        $storeId ??= config('opencart.store_id');

        return $query->whereHas('stores', fn ($q) => $q->where('store_id', $storeId));
    }
}
