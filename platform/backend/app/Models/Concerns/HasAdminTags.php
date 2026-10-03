<?php

namespace App\Models\Concerns;

use App\Models\AdminRecordTag;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasAdminTags
{
    public function adminTagRecord(): MorphOne
    {
        return $this->morphOne(AdminRecordTag::class, 'taggable');
    }

    public function adminTags(): array
    {
        return $this->adminTagRecord?->tags ?? [];
    }
}
