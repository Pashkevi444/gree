<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\PaymentMethod;
use PHPUnit\Framework\TestCase;

final class PaymentMethodTest extends TestCase
{
    public function testThreeMethodsFromTzAreAvailable(): void
    {
        $this->assertSame('card',      PaymentMethod::Card->value);
        $this->assertSame('uzum_bank', PaymentMethod::UzumBank->value);
        $this->assertSame('anor_bank', PaymentMethod::AnorBank->value);
    }

    public function testTranslationKeyMirrorsValue(): void
    {
        $this->assertSame('order.payment.card', PaymentMethod::Card->translationKey());
        $this->assertSame('order.payment.uzum_bank', PaymentMethod::UzumBank->translationKey());
    }

    public function testTryFromOrNullReturnsNullOnEmpty(): void
    {
        $this->assertNull(PaymentMethod::tryFromOrNull(null));
        $this->assertNull(PaymentMethod::tryFromOrNull(''));
        $this->assertNull(PaymentMethod::tryFromOrNull('paypal'));
        $this->assertSame(PaymentMethod::Card, PaymentMethod::tryFromOrNull('card'));
    }
}
