<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\DeliveryItemCollection;
use Gree\DTO\DeliveryItemDto;
use PHPUnit\Framework\TestCase;

final class DeliveryItemCollectionTest extends TestCase
{
    private function make(int $id = 1): DeliveryItemDto
    {
        return new DeliveryItemDto(id: $id, name: "D$id");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new DeliveryItemCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new DeliveryItemCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new DeliveryItemCollection())->add('x');
    }
}
