<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TrackedOrderEvent extends Model {
    protected $guarded = ['id'];
    protected function casts(): array { return ['visible_to_customer'=>'boolean']; }
}
