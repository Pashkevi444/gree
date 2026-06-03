<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\B2bCardDto;
use PHPUnit\Framework\TestCase;

final class B2bCardDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = B2bCardDto::fromArray([
            'id'          => '7',
            'step_number' => '2',
            'name'        => 'Застройщик',
            'description' => 'Бонус для жильцов',
        ]);

        $this->assertSame(7, $dto->id);
        $this->assertSame(2, $dto->stepNumber);
        $this->assertSame('Застройщик', $dto->name);
        $this->assertSame('Бонус для жильцов', $dto->description);
    }

    public function testDefaults(): void
    {
        $dto = B2bCardDto::fromArray(['id' => 1, 'name' => 'X']);
        $this->assertSame(0, $dto->stepNumber);
        $this->assertSame('', $dto->description);
    }
}
