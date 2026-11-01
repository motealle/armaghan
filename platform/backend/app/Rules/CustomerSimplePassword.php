<?php

declare(strict_types=1);
namespace App\Rules;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Customer-selected simple passwords; administrator policy remains separate. */
final class CustomerSimplePassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || mb_strlen($value) < 4 || mb_strlen($value) > 255 || trim($value) === '') {
            $fail('رمز باید حداقل چهار نویسه داشته باشد.');
        }
    }
    public static function normalizedPin(mixed $input): mixed
    {
        if (! is_string($input) || preg_match('/^[۰-۹٠-٩0-9]{4,255}$/u', $input) !== 1) return $input;
        return strtr($input, ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
    }
}
