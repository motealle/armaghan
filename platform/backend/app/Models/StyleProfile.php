<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name',
    'schema_version',
    'draft_styles',
    'draft_texts',
    'draft_css',
    'draft_checksum',
    'created_by',
    'updated_by',
])]
class StyleProfile extends Model implements \Spatie\MediaLibrary\HasMedia
{
    use \Spatie\MediaLibrary\InteractsWithMedia;
    public const MEDIA_COLLECTION = 'home-media';
    protected static function booting(): void {
        \Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory::setCustomPathGenerators(static::class, \App\Media\HomePathGenerator::class);
    }
    public function registerMediaCollections(): void {
        $this->addMediaCollection(self::MEDIA_COLLECTION)->useDisk('public')->acceptsMimeTypes(['image/jpeg','image/png','image/webp']);
    }
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        if ($media === null || $media->collection_name !== self::MEDIA_COLLECTION) return;
        $size = @getimagesize($media->getPath());
        $sourceWidth = is_array($size) ? max(1, (int) $size[0]) : 1;
        foreach (['thumb' => [320, 72], 'card' => [800, 78], 'detail' => [1600, 82]] as $name => [$width, $quality]) {
            $this->addMediaConversion($name)->performOnCollections(self::MEDIA_COLLECTION)
                ->width(min($width, $sourceWidth))->format('webp')->quality($quality)->nonQueued();
        }
    }

    public function versions(): HasMany
    {
        return $this->hasMany(StyleProfileVersion::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'schema_version' => 'integer',
            'draft_styles' => 'array',
            'draft_texts' => 'array',
        ];
    }
}
