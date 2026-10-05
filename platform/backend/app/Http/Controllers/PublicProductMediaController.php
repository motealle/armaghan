<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Media\ProductMediaVariant;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicProductMediaController extends Controller
{
    public function show(Media $media, string $variant, ProductMediaVariant $variants): BinaryFileResponse|Response
    {
        abort_unless(
            $media->model_type === Product::class
            && $media->collection_name === Product::MEDIA_COLLECTION,
            404
        );

        $allowed = ['thumb', 'card', 'detail'];
        abort_unless(in_array($variant, $allowed, true), 404);

        $candidates = array_values(array_unique(array_filter([
            $variant,
            $variant === 'detail' ? 'card' : null,
            $variant !== 'thumb' ? 'thumb' : null,
        ])));

        foreach ($candidates as $conversion) {
            $path = $variants->path($media, $conversion);
            if ($path !== null) {
                return response()->file($path, [
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                    'Content-Type' => str_ends_with(strtolower($path), '.webp') ? 'image/webp' : (string) $media->mime_type,
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        $original = $media->getPath();
        abort_unless(is_file($original) && filesize($original) > 0, 404);

        return response()->file($original, [
            'Cache-Control' => 'public, max-age=60',
            'Content-Type' => (string) $media->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
