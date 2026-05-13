<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\GreeCardDto;
use PHPUnit\Framework\TestCase;

final class GreeCardDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = GreeCardDto::fromArray([
            'id'          => '1',
            'name'        => 'Гарантия',
            'description' => '10 лет гарантии',
            'icon_code'   => 'thumbs-up',
        ]);

        $this->assertSame(1,             $dto->id);
        $this->assertSame('Гарантия',    $dto->name);
        $this->assertSame('10 лет гарантии', $dto->description);
        $this->assertSame('thumbs-up',   $dto->iconCode);
    }

    public function testFromArrayDefaults(): void
    {
        $dto = GreeCardDto::fromArray(['id' => '1', 'name' => 'X']);

        $this->assertSame('', $dto->description);
        $this->assertSame('', $dto->iconCode);
    }
}
