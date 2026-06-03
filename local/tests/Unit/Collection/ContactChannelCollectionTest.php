<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\ContactChannelCollection;
use Gree\DTO\ContactChannelDto;
use PHPUnit\Framework\TestCase;

final class ContactChannelCollectionTest extends TestCase
{
    private function make(int $id = 1): ContactChannelDto
    {
        return new ContactChannelDto(id: $id, name: "Channel $id");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new ContactChannelCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new ContactChannelCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ContactChannelCollection())->add('x');
    }
}
