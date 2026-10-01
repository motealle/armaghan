<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'names' => [
                'fa' => $this->name_fa,
                'ar' => $this->name_ar,
                'en' => $this->name_en,
                'ku' => $this->name_ku,
            ],
            'subcategories' => $this->subcategories
                ->map(fn ($subcategory): array => [
                    'code' => $subcategory->code,
                    'names' => [
                        'fa' => $subcategory->name_fa,
                        'ar' => $subcategory->name_ar,
                        'en' => $subcategory->name_en,
                        'ku' => $subcategory->name_ku,
                    ],
                ])
                ->values(),
        ];
    }
}
