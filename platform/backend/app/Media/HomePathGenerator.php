<?php
namespace App\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class HomePathGenerator extends ProductPathGenerator {
 public function getPath(Media $media): string { return 'media/home/'.$media->getKey().'/'; }
}
