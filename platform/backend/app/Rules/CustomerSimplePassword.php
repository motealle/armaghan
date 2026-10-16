<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Optional 6-digit numeric PIN for non-admin customers with limited digital literacy.
 * All other passwords keep the prior 8+ character and letter+number rule.
 * No admin password policies are changed.
 */
final class CustomerSimplePassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('رمز ورود معتبر نیست.');
            return;
        }
        if (preg_match('/^[0-9]{6}$/D', $value) === 1) {
            if (self::isPredictablePin($value)) {
                $fail('یک رمز شش‌رقمی غیرتکراری و غیرقابل‌حدس انتخاب کنید.');
            }
            return;
        }
        // Preserve the original strong 8+ letter-and-digit option; do not weaken
        // existing email, Google, administrator or password-change policies.
        if (mb_strlen($value) < 8 || preg_match('/\p{L}/u', $value) !== 1 || preg_match('/[0-9]/', $value) !== 1) {
            $fail('رمز باید شش رقم غیرقابل‌حدس یا حداقل هشت نویسه شامل حرف و عدد باشد.');
        }
    }

    public static function normalizedPin(mixed $input): mixed
    {
        if (! is_string($input) || preg_match('/^[۰-۹٠-٩0-9]{6}$/u', $input) !== 1) {
            return $input;
        }
        return strtr($input, [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ]);
    }

    private static function isPredictablePin(string $pin): bool
    {
        if (preg_match('/^(\d)\1{5}$/D', $pin) === 1) return true;
        if (preg_match('/^(\d\d)\1{2}$/D', $pin) === 1) return true;
        if (preg_match('/^(\d\d\d)\1$/D', $pin) === 1) return true;
        if (in_array($pin, ['012345','123456','234567','345678','456789',
                            '987654','876543','765432','654321','543210',
                            '112233','000001','111222','222333','123321','121212'], true)) return true;
        // Monotonic six digits, including near-sequential variations in both directions.
        $increments=[];
        for ($i=1; $i<6; $i++) $increments[]=(int) $pin[$i] - (int) $pin[$i - 1];
        return count(array_unique($increments)) === 1 && in_array($increments[0], [-1,1], true);
    }
}
