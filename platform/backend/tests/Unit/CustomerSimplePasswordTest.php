<?php

namespace Tests\Unit;

use App\Rules\CustomerSimplePassword;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CustomerSimplePasswordTest extends TestCase
{
    public function test_accepts_memorable_format_without_lowering_existing_strong_password_option(): void
    {
        foreach (['583921', 'Shop2026secure', 'فروشگاه۱۴۰۵', 'Shop٢٠٢٦secure'] as $input) {
            $result = Validator::make(['password'=>$input], ['password'=>[new CustomerSimplePassword()]]);
            $this->assertTrue($result->passes(), $input);
        }
        $this->assertSame('583921', CustomerSimplePassword::normalizedPin('۵۸۳۹۲۱'));
        $this->assertSame('583921', CustomerSimplePassword::normalizedPin('٥٨٣٩٢١'));
    }

    public function test_rejects_trivial_pins_and_weak_passwords(): void
    {
        foreach (['123456','654321','000000','111111','121212','123123','112233','abc123','abc1234','1234567'] as $input) {
            $result = Validator::make(['password'=>$input], ['password'=>[new CustomerSimplePassword()]]);
            $this->assertTrue($result->fails(), $input);
        }
        $this->assertSame('Shop2026secure', CustomerSimplePassword::normalizedPin('Shop2026secure'));
    }
}
