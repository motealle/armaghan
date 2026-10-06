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
        if ($media->model instanceof \App\Models\TrackedOrder && $media->mime_type === 'application/pdf') return;

        if (! (($media->model instanceof Product && $media->collection_name === Product::MEDIA_COLLECTION) || ($media->model instanceof \App\Models\StyleProfile && $media->collection_name === \App\Models\StyleProfile::MEDIA_COLLECTION) || ($media->model instanceof \App\Models\TrackedOrder && $media->collection_name === \App\Models\TrackedOrder::MEDIA_COLLECTION))) {
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

        if ($media->model instanceof Product && $media->collection_name === Product::MEDIA_COLLECTION) {
            $image = $this->resizeWithin($image, 1920);
            $image = $this->portraitFrame($image);
        } elseif ($media->model instanceof \App\Models\StyleProfile && $media->collection_name === \App\Models\StyleProfile::MEDIA_COLLECTION) {
            $image = $this->resizeWithin($image, 2200);
        }

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
                'custom_properties' => array_merge($media->custom_properties ?? [], ['width' => imagesx($image), 'height' => imagesy($image)]),
            ])->saveQuietly();
        } catch (\Throwable $exception) {
            @unlink($temporary);
            $media->delete();

            throw $exception;
        } finally {
            imagedestroy($image);
        }
    }

    private function resizeWithin(GdImage $image, int $maxEdge): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $largest = max($width, $height);

        if ($largest <= $maxEdge) {
            return $image;
        }

        $scale = $maxEdge / $largest;
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefill($resized, 0, 0, $transparent);

        if (! imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height)) {
            imagedestroy($resized);
            throw new RuntimeException('Product image resize failed.');
        }

        imagedestroy($image);

        return $resized;
    }

    private function portraitFrame(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($height > $width) {
            return $image;
        }

        // Landscape/square uploads stay fully visible. We frame them into a 3:4 portrait
        // master so cards remain consistent without destructive crop or stretching.
        $targetWidth = min($width, 1440);
        $targetHeight = max($targetWidth + 1, (int) round($targetWidth * 4 / 3));
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, true);

        $corners = [
            imagecolorat($image, 0, 0),
            imagecolorat($image, max(0, $width - 1), 0),
            imagecolorat($image, 0, max(0, $height - 1)),
            imagecolorat($image, max(0, $width - 1), max(0, $height - 1)),
        ];
        $red = $green = $blue = 0;
        foreach ($corners as $color) {
            $red += ($color >> 16) & 0xff;
            $green += ($color >> 8) & 0xff;
            $blue += $color & 0xff;
        }
        $background = imagecolorallocate($canvas, (int) round($red / 4), (int) round($green / 4), (int) round($blue / 4));
        imagefill($canvas, 0, 0, $background);

        $coverScale = max($targetWidth / $width, $targetHeight / $height);
        $coverWidth = max(1, (int) ceil($width * $coverScale));
        $coverHeight = max(1, (int) ceil($height * $coverScale));
        $coverX = (int) floor(($targetWidth - $coverWidth) / 2);
        $coverY = (int) floor(($targetHeight - $coverHeight) / 2);
        imagecopyresampled($canvas, $image, $coverX, $coverY, 0, 0, $coverWidth, $coverHeight, $width, $height);
        if (function_exists('imagefilter')) {
            @imagefilter($canvas, IMG_FILTER_GAUSSIAN_BLUR);
            @imagefilter($canvas, IMG_FILTER_GAUSSIAN_BLUR);
        }
        $veil = imagecolorallocatealpha($canvas, 255, 255, 255, 76);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $veil);

        $fitScale = min(($targetWidth * 0.94) / $width, ($targetHeight * 0.94) / $height);
        $fitWidth = max(1, (int) round($width * $fitScale));
        $fitHeight = max(1, (int) round($height * $fitScale));
        $fitX = (int) floor(($targetWidth - $fitWidth) / 2);
        $fitY = (int) floor(($targetHeight - $fitHeight) / 2);

        if (! imagecopyresampled($canvas, $image, $fitX, $fitY, 0, 0, $fitWidth, $fitHeight, $width, $height)) {
            imagedestroy($canvas);
            throw new RuntimeException('Product image portrait framing failed.');
        }

        imagedestroy($image);

        return $canvas;
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
