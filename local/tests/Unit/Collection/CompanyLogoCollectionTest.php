<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\CompanyLogoCollection;
use Gree\DTO\BrandLogoDto;
use PHPUnit\Framework\TestCase;

final class CompanyLogoCollectionTest extends TestCase
{
    private function make(int $id = 1): BrandLogoDto
    {
        return new BrandLogoDto(id: $id, name: "Co$id", imageUrl: '/x.png');
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new CompanyLogoCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new CompanyLogoCollection($this->make(1));
        $c->add($this->make(2));
        $this->assertCount(2, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CompanyLogoCollection())->add('x');
    }
}
