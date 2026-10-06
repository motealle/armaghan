<?php

namespace App\Services\Media;

use GdImage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** Repair legacy/missing derivatives without a queue, changing originals, or public helpers. */
class ProductMediaVariant
{
    public function path(Media $media, string $variant, bool $recordGenerated = true): ?string
    {
        $path = $media->getPath($variant);
        if (is_file($path) && filesize($path) > 0) {
            return $path;
        }

        $original = $media->getPath();
        $size = is_file($original) ? @getimagesize($original) : false;
        if (! is_array($size) || $size[0] < 1 || $size[1] < 1 || $size[0] > 5000 || $size[1] > 5000 || ! function_exists('imagewebp')) {
            return null;
        }
        $directory = dirname($path);
        if (! is_dir($directory) && ! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return null;
        }
        $lock = @fopen($directory.'/.variant.lock', 'c');
        if ($lock === false) return null;
        $source = $target = null;
        $temporary = null;
        try {
            // A second request must not block the PHP worker on the first conversion.
            if (! flock($lock, LOCK_EX | LOCK_NB)) return null;
            clearstatcache(true, $path);
            if (is_file($path) && filesize($path) > 0) return $path;
            $source = match ($size['mime'] ?? '') {
                'image/jpeg' => @imagecreatefromjpeg($original),
                'image/png' => @imagecreatefrompng($original),
                'image/webp' => @imagecreatefromwebp($original),
                default => false,
            };
            if (! $source instanceof GdImage) return null;
            [$maximum, $quality] = match ($variant) {
                'thumb' => [320, 72],
                'card' => [800, 78],
                'detail' => [1600, 82],
            };
            $width = min($maximum, $size[0]);
            $height = max(1, (int) round($size[1] * $width / $size[0]));
            $target = imagecreatetruecolor($width, $height);
            if (! $target instanceof GdImage) return null;
            imagealphablending($target, false);
            imagesavealpha($target, true);
            imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
            if (! imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1])) return null;
            $temporary = $path.'.tmp-'.bin2hex(random_bytes(6));
            if (! imagewebp($target, $temporary, $quality) || ! is_file($temporary) || filesize($temporary) < 1) return null;
            if (! @rename($temporary, $path)) return null;
            @chmod($path, 0644);
            if ($recordGenerated) $media->markAsConversionGenerated($variant);
            return $path;
        } finally {
            if ($temporary && is_file($temporary)) @unlink($temporary);
            if ($source instanceof GdImage) imagedestroy($source);
            if ($target instanceof GdImage) imagedestroy($target);
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
