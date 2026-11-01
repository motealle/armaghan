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
        $value = preg_replace('/^(\+(?:964|98|90|971))0(?=\d)/', '$1', $value) ?? '';
        // Require explicit country code: silently assuming IR instead of IQ would link wrong people.
        return preg_match('/^\+[1-9][0-9]{7,14}$/D', $value) ? $value : null;
    }

    public static function username(string $value): ?string
    {
        $value = mb_strtolower(trim($value));
        $value = strtr($value, ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
        return preg_match('/^[\p{L}\p{N}][\p{L}\p{N}._-]{0,31}$/uD', $value) ? $value : null;
    }
}
