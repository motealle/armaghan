<?php

namespace App\Models;

use App\Enums\ProductAvailability;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'subcategory_id',
    'code',
    'name_fa',
    'name_ar',
    'name_en',
    'name_ku',
    'availability',
    'active',
    'sort_order',
])]
class Product extends Model
{
    use HasFactory;

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function specValues(): HasMany
    {
        return $this->hasMany(ProductSpecValue::class);
    }

    public function favoriteShares(): BelongsToMany
    {
        return $this->belongsToMany(FavoriteShare::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'availability' => ProductAvailability::class,
            'sort_order' => 'integer',
        ];
    }
}
