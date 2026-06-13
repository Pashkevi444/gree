<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\WhereToBuyLocationCollection;
use Gree\DTO\WhereToBuyLocationDto;
use PHPUnit\Framework\TestCase;

final class WhereToBuyLocationCollectionTest extends TestCase
{
    private function make(int $id = 1): WhereToBuyLocationDto
    {
        return new WhereToBuyLocationDto(
            id: $id, name: "L$id", schedule: '', phones: [],
            imageUrl: '', latitude: '', longitude: '',
        );
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new WhereToBuyLocationCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new WhereToBuyLocationCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new WhereToBuyLocationCollection())->add('x');
    }
}
