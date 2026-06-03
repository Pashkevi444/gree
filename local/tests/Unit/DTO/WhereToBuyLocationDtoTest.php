<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\WhereToBuyLocationDto;
use PHPUnit\Framework\TestCase;

final class WhereToBuyLocationDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = WhereToBuyLocationDto::fromArray([
            'id' => 3, 'name' => 'Бунёдкор', 'schedule' => '9–18',
            'phones' => ['+998 974 72 07 07'],
            'image_url' => '/upload/b.png',
            'latitude' => '41.26', 'longitude' => '69.20',
        ]);

        $this->assertSame(3, $dto->id);
        $this->assertSame('Бунёдкор', $dto->name);
        $this->assertSame(['+998 974 72 07 07'], $dto->phones);
        $this->assertSame('41.26', $dto->latitude);
        $this->assertSame('69.20', $dto->longitude);
    }
}
