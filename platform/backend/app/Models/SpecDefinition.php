<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'subcategory_id',
    'key',
    'label_fa',
    'label_ar',
    'label_en',
    'label_ku',
    'locked',
    'sort_order',
])]
class SpecDefinition extends Model
{
    use HasFactory;

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductSpecValue::class);
    }

    protected function casts(): array
    {
        return [
            'locked' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
