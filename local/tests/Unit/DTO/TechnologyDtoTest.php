<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\TechnologyDto;
use PHPUnit\Framework\TestCase;

final class TechnologyDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = TechnologyDto::fromArray([
            'id'          => '5',
            'name'        => 'Инвертор',
            'description' => 'Экономия 40%',
            'image'       => '/upload/tech.png',
        ]);

        $this->assertSame(5,            $dto->id);
        $this->assertSame('Инвертор',   $dto->name);
        $this->assertSame('Экономия 40%', $dto->description);
        $this->assertSame('/upload/tech.png', $dto->image);
    }

    public function testFromArrayDefaults(): void
    {
        $dto = TechnologyDto::fromArray(['id' => '1', 'name' => 'X']);

        $this->assertSame('', $dto->description);
        $this->assertSame('', $dto->image);
    }
}
