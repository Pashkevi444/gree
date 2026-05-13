<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\BrandAboutCardCollection;
use Gree\DTO\BrandAboutCardDto;
use PHPUnit\Framework\TestCase;

final class BrandAboutCardCollectionTest extends TestCase
{
    private function makeCard(int $id = 1): BrandAboutCardDto
    {
        return new BrandAboutCardDto(id: $id, name: "Card $id", description: "Desc $id");
    }

    public function testEmptyCollection(): void
    {
        $this->assertCount(0, new BrandAboutCardCollection());
    }

    public function testConstructWithItems(): void
    {
        $c = new BrandAboutCardCollection($this->makeCard(1), $this->makeCard(2));
        $this->assertCount(2, $c);
    }

    public function testAddValidItem(): void
    {
        $c = new BrandAboutCardCollection();
        $c->add($this->makeCard());
        $this->assertCount(1, $c);
    }

    public function testAddInvalidItemThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new BrandAboutCardCollection())->add('not a card');
    }
}
