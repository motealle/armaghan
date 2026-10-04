<?php

namespace App\Models;

use App\Enums\ProductAvailability;
use App\Media\ProductPathGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

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
class Product extends Model implements HasMedia
{
    use HasFactory;
    use \App\Models\Concerns\HasAdminTags;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION = 'product-gallery';

    protected static function booting(): void
    {
        PathGeneratorFactory::setCustomPathGenerators(
            static::class,
            ProductPathGenerator::class,
        );
    }

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

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::MEDIA_COLLECTION)
            ->useDisk('public')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media === null || $media->collection_name !== self::MEDIA_COLLECTION) {
            return;
        }

        $size = @getimagesize($media->getPath());
        $sourceWidth = is_array($size) ? max(1, (int) ($size[0] ?? 1)) : 1;

        foreach ([
            'thumb' => [320, 72],
            'card' => [800, 78],
            'detail' => [1600, 82],
        ] as $name => [$width, $quality]) {
            $this
                ->addMediaConversion($name)
                ->performOnCollections(self::MEDIA_COLLECTION)
                ->width(min($width, $sourceWidth))
                ->format('webp')
                ->quality($quality)
                ->nonQueued();
        }
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
