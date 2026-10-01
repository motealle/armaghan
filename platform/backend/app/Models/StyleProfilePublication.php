<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'channel',
    'style_profile_version_id',
    'published_by',
    'published_at',
])]
class StyleProfilePublication extends Model
{
    public function version(): BelongsTo
    {
        return $this->belongsTo(StyleProfileVersion::class, 'style_profile_version_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
