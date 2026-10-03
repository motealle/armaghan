<?php
namespace App\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class OrderPathGenerator extends ProductPathGenerator {
 public function getPath(Media $media): string {return 'media/orders/'.$media->getKey().'/';}
}
