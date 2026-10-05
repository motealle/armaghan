<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicProductResource extends JsonResource
{
    private function mediaDimensions(Media $media): array
    {
        $width = (int) $media->getCustomProperty('width', 0);
        $height = (int) $media->getCustomProperty('height', 0);
        if ($width < 1 || $height < 1) {
            $size = @getimagesize($media->getPath());
            $width = is_array($size) ? (int) $size[0] : 0;
            $height = is_array($size) ? (int) $size[1] : 0;
        }
        return ['width' => $width ?: null, 'height' => $height ?: null];
    }

    public function toArray(Request $request): array
    {
        $subcategory = $this->subcategory;
        $category = $subcategory?->category;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'names' => [
                'fa' => $this->name_fa,
                'ar' => $this->name_ar,
                'en' => $this->name_en,
                'ku' => $this->name_ku,
            ],
            'availability' => $this->availability->value,
            'sort_order' => $this->sort_order,
            'specifications' => $subcategory?->specDefinitions->sortBy('sort_order')->values()->map(fn ($d) => [
                'key' => $d->key, 'locked' => $d->locked,
                'labels' => ['fa' => $d->label_fa, 'ar' => $d->label_ar, 'en' => $d->label_en, 'ku' => $d->label_ku],
                'value_text' => $this->specValues->firstWhere('spec_definition_id', $d->id)?->value_text,
            ])->all() ?? [],
            'media' => $this->media
                ->where('collection_name', Product::MEDIA_COLLECTION)
                ->sortBy(fn (Media $media): int => $media->order_column ?? PHP_INT_MAX)
                ->values()
                ->map(fn (Media $media): array => [
                    'id' => (string) ($media->uuid ?: $media->id),
                    'url' => route('catalog.product-media', ['media' => $media->id, 'variant' => 'card']),
                    'thumb_url' => route('catalog.product-media', ['media' => $media->id, 'variant' => 'thumb']),
                    'detail_url' => route('catalog.product-media', ['media' => $media->id, 'variant' => 'detail']),
                    ...$this->mediaDimensions($media),
                ])
                ->all(),
            'category' => $category === null ? null : [
                'code' => $category->code,
                'names' => [
                    'fa' => $category->name_fa,
                    'ar' => $category->name_ar,
                    'en' => $category->name_en,
                    'ku' => $category->name_ku,
                ],
            ],
            'subcategory' => $subcategory === null ? null : [
                'code' => $subcategory->code,
                'names' => [
                    'fa' => $subcategory->name_fa,
                    'ar' => $subcategory->name_ar,
                    'en' => $subcategory->name_en,
                    'ku' => $subcategory->name_ku,
                ],
            ],
        ];
    }
}
