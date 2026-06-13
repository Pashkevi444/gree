<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\ServiceCardCollection;
use Gree\DTO\ServiceCardDto;
use PHPUnit\Framework\TestCase;

final class ServiceCardCollectionTest extends TestCase
{
    private function make(int $id = 1): ServiceCardDto
    {
        return new ServiceCardDto(id: $id, name: "C$id");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new ServiceCardCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new ServiceCardCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ServiceCardCollection())->add('x');
    }
}
