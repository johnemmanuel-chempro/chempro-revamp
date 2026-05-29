<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends OpenCartModel
{
    protected $table = 'category';

    protected $primaryKey = 'category_id';

    public function description(): HasOne
    {
        return $this->hasOne(CategoryDescription::class, 'category_id', 'category_id')
            ->where('language_id', config('opencart.language_id'));
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'category_id');
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(
            Store::class,
            'category_to_store',
            'category_id',
            'store_id'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForStore($query, ?int $storeId = null)
    {
        $storeId ??= config('opencart.store_id');

        return $query->whereExists(function ($sub) use ($storeId) {
            $sub->from('category_to_store')
                ->whereColumn('category_to_store.category_id', 'category.category_id')
                ->where('category_to_store.store_id', $storeId);
        });
    }
}
