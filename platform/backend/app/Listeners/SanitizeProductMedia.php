<?php

namespace App\Listeners;

use App\Models\Product;
use GdImage;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class SanitizeProductMedia
{
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;

        if (! $media->model instanceof Product || $media->collection_name !== Product::MEDIA_COLLECTION) {
            return;
        }

        $path = $media->getPath();
        $mime = (string) $media->mime_type;

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $image instanceof GdImage) {
            $media->delete();

            throw new RuntimeException('Uploaded product image could not be decoded safely.');
        }

        $image = $this->normalizeOrientation($image, $path, $mime);
        $temporary = $path.'.sanitized-'.bin2hex(random_bytes(4));

        try {
            $saved = match ($mime) {
                'image/jpeg' => imagejpeg($image, $temporary, 90),
                'image/png' => $this->savePng($image, $temporary),
                'image/webp' => function_exists('imagewebp') && imagewebp($image, $temporary, 88),
                default => false,
            };

            if (! $saved || ! is_file($temporary) || filesize($temporary) <= 0) {
                throw new RuntimeException('Product image sanitization failed.');
            }

            if (! @rename($temporary, $path)) {
                throw new RuntimeException('Sanitized product image could not replace the upload.');
            }

            @chmod($path, 0644);
            clearstatcache(true, $path);

            $media->forceFill([
                'size' => filesize($path),
            ])->saveQuietly();
        } catch (\Throwable $exception) {
            @unlink($temporary);
            $media->delete();

            throw $exception;
        } finally {
            imagedestroy($image);
        }
    }

    private function savePng(GdImage $image, string $path): bool
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);

        return imagepng($image, $path, 6);
    }

    private function normalizeOrientation(GdImage $image, string $path, string $mime): GdImage
    {
        if ($mime !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

        $degrees = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($degrees === 0) {
            return $image;
        }

        $rotated = @imagerotate($image, $degrees, 0);
        if (! $rotated instanceof GdImage) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }
}
