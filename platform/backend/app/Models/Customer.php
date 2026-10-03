<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'country_code',
    'country_name',
    'whatsapp',
    'company_name',
    'notes',
    'priority',
    'active',
    'direct_link_enabled',
])]
class Customer extends Model
{
    use HasFactory;
    use \App\Models\Concerns\HasAdminTags;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteShares(): HasMany
    {
        return $this->hasMany(FavoriteShare::class);
    }

    public function magicLinks(): HasMany
    {
        return $this->hasMany(MagicLink::class);
    }

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'direct_link_enabled' => 'boolean',
            'priority' => 'integer',
        ];
    }
}
