<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\ContactAddressCollection;
use Gree\DTO\ContactAddressDto;
use PHPUnit\Framework\TestCase;

final class ContactAddressCollectionTest extends TestCase
{
    private function make(int $id = 1): ContactAddressDto
    {
        return new ContactAddressDto(
            id: $id, name: "A$id", schedule: '', phones: [],
            imageUrl: '', latitude: '0', longitude: '0',
        );
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new ContactAddressCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new ContactAddressCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ContactAddressCollection())->add('x');
    }
}
