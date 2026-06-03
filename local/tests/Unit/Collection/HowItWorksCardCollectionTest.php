<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\HowItWorksCardCollection;
use Gree\DTO\HowItWorksCardDto;
use PHPUnit\Framework\TestCase;

final class HowItWorksCardCollectionTest extends TestCase
{
    private function make(int $id = 1): HowItWorksCardDto
    {
        return new HowItWorksCardDto(id: $id, name: "H$id");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new HowItWorksCardCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new HowItWorksCardCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new HowItWorksCardCollection())->add('x');
    }
}
