<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name',
    'schema_version',
    'draft_styles',
    'draft_texts',
    'draft_css',
    'draft_checksum',
    'created_by',
    'updated_by',
])]
class StyleProfile extends Model
{
    public function versions(): HasMany
    {
        return $this->hasMany(StyleProfileVersion::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'schema_version' => 'integer',
            'draft_styles' => 'array',
            'draft_texts' => 'array',
        ];
    }
}
