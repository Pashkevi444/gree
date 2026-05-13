<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\GreeStatDto;
use PHPUnit\Framework\TestCase;

final class GreeStatDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = GreeStatDto::fromArray([
            'id'            => '2',
            'name'          => '№1 в мире',
            'number_prefix' => '№',
            'number_value'  => '1',
            'number_suffix' => 'в мире',
            'description'   => 'По производству сплит-систем',
        ]);

        $this->assertSame(2,                           $dto->id);
        $this->assertSame('№',                         $dto->numberPrefix);
        $this->assertSame(1,                           $dto->numberValue);
        $this->assertSame('в мире',                    $dto->numberSuffix);
        $this->assertSame('По производству сплит-систем', $dto->description);
    }

    public function testFromArrayDefaults(): void
    {
        $dto = GreeStatDto::fromArray(['id' => '1', 'name' => 'X', 'number_value' => '5']);

        $this->assertSame('', $dto->numberPrefix);
        $this->assertSame('', $dto->numberSuffix);
        $this->assertSame('', $dto->description);
    }
}
