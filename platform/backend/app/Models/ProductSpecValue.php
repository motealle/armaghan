<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'spec_definition_id', 'value_text'])]
class ProductSpecValue extends Model
{
    use HasFactory;

    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductSpecTranslation::class);
    }

    public function localizedValues(): array
    {
        return ['fa' => $this->value_text, 'ar' => null, 'en' => null, 'ku' => null,
            ...$this->translations->pluck('value_text', 'locale')->all()];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(SpecDefinition::class, 'spec_definition_id');
    }
}
