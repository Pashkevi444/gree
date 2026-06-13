<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\ServiceHeroDto;
use PHPUnit\Framework\TestCase;

final class ServiceHeroDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = ServiceHeroDto::fromArray([
            'id'             => 1,
            'name'           => 'Hero title',
            'description'    => 'Hero desc',
            'background_url' => '/upload/x.png',
        ]);

        $this->assertSame(1, $dto->id);
        $this->assertSame('Hero title', $dto->name);
        $this->assertSame('Hero desc', $dto->description);
        $this->assertSame('/upload/x.png', $dto->backgroundUrl);
    }

    public function testDefaults(): void
    {
        $dto = ServiceHeroDto::fromArray(['id' => 1, 'name' => 'X']);
        $this->assertSame('', $dto->description);
        $this->assertSame('', $dto->backgroundUrl);
    }
}
