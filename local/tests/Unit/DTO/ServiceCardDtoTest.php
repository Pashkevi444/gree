<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\ServiceCardDto;
use PHPUnit\Framework\TestCase;

final class ServiceCardDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = ServiceCardDto::fromArray([
            'id'          => 3,
            'name'        => 'Установка',
            'description' => 'Сертифицированный',
            'icon_code'   => 'check',
        ]);

        $this->assertSame(3, $dto->id);
        $this->assertSame('Установка', $dto->name);
        $this->assertSame('Сертифицированный', $dto->description);
        $this->assertSame('check', $dto->iconCode);
    }
}
