<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\ServiceFeatureCollection;
use Gree\DTO\ServiceFeatureDto;
use PHPUnit\Framework\TestCase;

final class ServiceFeatureCollectionTest extends TestCase
{
    private function make(int $id = 1): ServiceFeatureDto
    {
        return new ServiceFeatureDto(id: $id, name: "F$id");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new ServiceFeatureCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new ServiceFeatureCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ServiceFeatureCollection())->add('x');
    }
}
