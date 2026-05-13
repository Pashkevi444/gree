<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\BrandAboutCardDto;
use PHPUnit\Framework\TestCase;

final class BrandAboutCardDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = BrandAboutCardDto::fromArray([
            'id'          => 3,
            'name'        => 'Достижения',
            'description' => 'Мировой лидер...',
        ]);

        $this->assertSame(3, $dto->id);
        $this->assertSame('Достижения', $dto->name);
        $this->assertSame('Мировой лидер...', $dto->description);
    }

    public function testDescriptionDefaultsToEmpty(): void
    {
        $dto = BrandAboutCardDto::fromArray(['id' => 1, 'name' => 'X']);

        $this->assertSame('', $dto->description);
    }
}
