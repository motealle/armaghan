<?php

namespace Tests\Unit;

use App\Rules\CustomerSimplePassword;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CustomerSimplePasswordTest extends TestCase
{
    public function test_accepts_memorable_format_without_lowering_existing_strong_password_option(): void
    {
        foreach (['123456', '1111', 'aaaa', '583921', 'Shop2026secure', 'فروشگاه۱۴۰۵', 'Shop٢٠٢٦secure'] as $input) {
            $result = Validator::make(['password'=>$input], ['password'=>[new CustomerSimplePassword()]]);
            $this->assertTrue($result->passes(), $input);
        }
        $this->assertSame('583921', CustomerSimplePassword::normalizedPin('۵۸۳۹۲۱'));
        $this->assertSame('583921', CustomerSimplePassword::normalizedPin('٥٨٣٩٢١'));
    }

    public function test_rejects_empty_and_too_short_passwords(): void
    {
        foreach (['','1','12','123','   '] as $input) {
            $result = Validator::make(['password'=>$input], ['password'=>[new CustomerSimplePassword()]]);
            $this->assertTrue($result->fails(), $input);
        }
        $this->assertSame('Shop2026secure', CustomerSimplePassword::normalizedPin('Shop2026secure'));
    }
}
