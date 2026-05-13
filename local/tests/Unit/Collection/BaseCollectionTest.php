<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\BaseCollection;
use PHPUnit\Framework\TestCase;

final class BaseCollectionTest extends TestCase
{
    private function makeCollection(\stdClass ...$items): BaseCollection
    {
        return new class(...$items) extends BaseCollection {
            public function __construct(\stdClass ...$items)
            {
                parent::__construct(array_values($items));
            }

            protected function itemClass(): string
            {
                return \stdClass::class;
            }
        };
    }

    public function testAddValidItemSucceeds(): void
    {
        $c = $this->makeCollection();
        $c->add(new \stdClass());

        $this->assertCount(1, $c);
    }

    public function testAddWrongTypeThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $c = $this->makeCollection();
        $c->add('not an object');
    }

    public function testFilterReturnsSubclassInstance(): void
    {
        $a = new \stdClass();
        $b = new \stdClass();
        $c = $this->makeCollection($a, $b);

        $filtered = $c->filter(fn() => true);

        $this->assertInstanceOf($c::class, $filtered);
    }

    public function testMapReturnsNewCollectionWithSameType(): void
    {
        $item = new \stdClass();
        $c    = $this->makeCollection($item);

        $cloned = $c->map(fn($i) => clone $i);

        $this->assertInstanceOf($c::class, $cloned);
    }

    public function testEmptyConstructorProducesEmptyCollection(): void
    {
        $c = $this->makeCollection();

        $this->assertCount(0, $c);
        $this->assertTrue($c->isEmpty());
    }
}
