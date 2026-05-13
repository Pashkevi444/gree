<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\BrandHistoryDto;
use PHPUnit\Framework\TestCase;

final class BrandHistoryDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = BrandHistoryDto::fromArray([
            'id'   => 1,
            'name' => 'История бренда',
            'text' => '<p>Текст истории</p>',
        ]);

        $this->assertSame(1, $dto->id);
        $this->assertSame('История бренда', $dto->name);
        $this->assertSame('<p>Текст истории</p>', $dto->text);
    }

    public function testFromArrayDefaults(): void
    {
        $dto = BrandHistoryDto::fromArray(['id' => 0, 'name' => '', 'text' => '']);

        $this->assertSame(0, $dto->id);
        $this->assertSame('', $dto->name);
        $this->assertSame('', $dto->text);
    }
}
