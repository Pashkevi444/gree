<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\DeliveryItemDto;
use PHPUnit\Framework\TestCase;

final class DeliveryItemDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = DeliveryItemDto::fromArray([
            'id'          => 2,
            'name'        => 'Бесплатно',
            'description' => 'Срок 1 день',
            'icon_code'   => 'check',
        ]);

        $this->assertSame(2, $dto->id);
        $this->assertSame('Бесплатно', $dto->name);
        $this->assertSame('Срок 1 день', $dto->description);
        $this->assertSame('check', $dto->iconCode);
    }

    public function testDefaults(): void
    {
        $dto = DeliveryItemDto::fromArray(['id' => 1, 'name' => 'X']);
        $this->assertSame('', $dto->description);
        $this->assertSame('', $dto->iconCode);
    }
}
