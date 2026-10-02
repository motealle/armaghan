<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicProductResource extends JsonResource
{
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
            'media' => $this->media
                ->where('collection_name', Product::MEDIA_COLLECTION)
                ->sortBy(fn (Media $media): int => $media->order_column ?? PHP_INT_MAX)
                ->values()
                ->map(fn (Media $media): array => [
                    'id' => (string) ($media->uuid ?: $media->id),
                    'url' => $media->hasGeneratedConversion('card')
                        ? $media->getFullUrl('card')
                        : $media->getFullUrl(),
                    'thumb_url' => $media->hasGeneratedConversion('thumb')
                        ? $media->getFullUrl('thumb')
                        : $media->getFullUrl(),
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
