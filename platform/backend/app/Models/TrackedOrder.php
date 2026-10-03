<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TrackedOrder extends Model {
    protected $guarded = ['id'];
    protected function casts(): array { return ['invoice_confirmed_at'=>'datetime','deposit_confirmed_at'=>'datetime']; }
    public function events(): HasMany { return $this->hasMany(TrackedOrderEvent::class); }
}
