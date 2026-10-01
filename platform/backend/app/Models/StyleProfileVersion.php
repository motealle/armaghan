<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'style_profile_id',
    'version',
    'schema_version',
    'source_test',
    'styles',
    'texts',
    'compiled_css',
    'checksum',
    'created_by',
])]
class StyleProfileVersion extends Model
{
    public function profile(): BelongsTo
    {
        return $this->belongsTo(StyleProfile::class, 'style_profile_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publications(): HasMany
    {
        return $this->hasMany(StyleProfilePublication::class);
    }

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'schema_version' => 'integer',
            'styles' => 'array',
            'texts' => 'array',
        ];
    }
}
