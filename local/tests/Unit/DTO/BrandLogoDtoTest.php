<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\BrandLogoDto;
use PHPUnit\Framework\TestCase;

final class BrandLogoDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = BrandLogoDto::fromArray([
            'id' => 12, 'name' => 'Партнёр', 'image_url' => '/upload/p.png',
        ]);
        $this->assertSame(12, $dto->id);
        $this->assertSame('Партнёр', $dto->name);
        $this->assertSame('/upload/p.png', $dto->imageUrl);
    }
}
