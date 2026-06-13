<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\PaymentMethodCollection;
use Gree\DTO\PaymentMethodDto;
use PHPUnit\Framework\TestCase;

final class PaymentMethodCollectionTest extends TestCase
{
    private function make(int $id = 1): PaymentMethodDto
    {
        return new PaymentMethodDto(id: $id, name: "M$id");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new PaymentMethodCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new PaymentMethodCollection($this->make(1), $this->make(2));
        $c->add($this->make(3));
        $this->assertCount(3, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new PaymentMethodCollection())->add('x');
    }
}
