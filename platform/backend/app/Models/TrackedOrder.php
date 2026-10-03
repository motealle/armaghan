<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TrackedOrder extends Model implements \Spatie\MediaLibrary\HasMedia {
 use \Spatie\MediaLibrary\InteractsWithMedia;
 public const MEDIA_COLLECTION='order-documents';
 protected static function booting(): void {\Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory::setCustomPathGenerators(static::class,\App\Media\OrderPathGenerator::class);}
 public function registerMediaCollections(): void {$this->addMediaCollection(self::MEDIA_COLLECTION)->useDisk('local')->acceptsMimeTypes(['application/pdf','image/jpeg','image/png','image/webp']);}

    protected $guarded = ['id'];
    protected function casts(): array { return ['invoice_confirmed_at'=>'datetime','deposit_confirmed_at'=>'datetime']; }
    public function events(): HasMany { return $this->hasMany(TrackedOrderEvent::class); }
}
