<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name_fa', 'name_ar', 'name_en', 'name_ku', 'active', 'sort_order'])]
class Category extends Model
{
    use HasFactory;

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
