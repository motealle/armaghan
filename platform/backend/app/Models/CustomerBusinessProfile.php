<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['contact_name', 'contact_email', 'phone', 'preferred_language', 'city', 'district',
    'shop_number', 'sales_product_group', 'purchase_volume', 'cooperation_type', 'sales_type', 'pinned'])]
class CustomerBusinessProfile extends Model
{
    public const FIELDS = ['contact_name', 'contact_email', 'phone', 'preferred_language', 'city', 'district',
        'shop_number', 'sales_product_group', 'purchase_volume', 'cooperation_type', 'sales_type', 'pinned'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected function casts(): array
    {
        return ['pinned' => 'boolean'];
    }

    public static function defaults(): array
    {
        return array_merge(array_fill_keys(self::FIELDS, null), ['pinned' => false]);
    }
}
