<?php

namespace App\Support;

final class CustomerPhone
{
    public static function normalize(string $value): ?string
    {
        // Allow Persian/Arabic numeric keyboards without creating duplicate identities.
        $value = strtr(trim($value), [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ]);
        $value = preg_replace('/[\s.()\-]+/u', '', $value) ?? '';
        if (str_starts_with($value, '00')) $value = '+'.substr($value, 2);
        // Require explicit country code: silently assuming IR instead of IQ would link wrong people.
        return preg_match('/^\+[1-9][0-9]{7,14}$/D', $value) ? $value : null;
    }

    public static function username(string $value): ?string
    {
        $value = strtolower(trim($value));
        return preg_match('/^[a-z][a-z0-9._-]{2,31}$/D', $value) ? $value : null;
    }
}
