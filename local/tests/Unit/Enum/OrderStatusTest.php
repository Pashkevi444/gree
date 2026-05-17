<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\OrderStatus;
use PHPUnit\Framework\TestCase;

final class OrderStatusTest extends TestCase
{
    public function testFiveCasesAreDefined(): void
    {
        $this->assertCount(5, OrderStatus::cases());
    }

    public function testNewIsDefaultEntryPoint(): void
    {
        $this->assertSame('new', OrderStatus::New->value);
        $this->assertSame('Новый', OrderStatus::New->label());
    }

    public function testValueRoundTrip(): void
    {
        foreach (OrderStatus::cases() as $status) {
            $this->assertSame($status, OrderStatus::from($status->value));
        }
    }
}
