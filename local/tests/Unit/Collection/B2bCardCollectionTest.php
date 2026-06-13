<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\B2bCardCollection;
use Gree\DTO\B2bCardDto;
use PHPUnit\Framework\TestCase;

final class B2bCardCollectionTest extends TestCase
{
    private function make(int $id = 1): B2bCardDto
    {
        return new B2bCardDto(id: $id, stepNumber: $id, name: "B$id");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new B2bCardCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new B2bCardCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new B2bCardCollection())->add('x');
    }
}
