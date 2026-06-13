<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\HelpStepDto;
use PHPUnit\Framework\TestCase;

final class HelpStepDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = HelpStepDto::fromArray([
            'id'          => 11,
            'step_number' => '3',
            'name'        => 'Дождаться',
            'description' => 'Проверка',
            'tooltip'     => 'Подсказка',
        ]);

        $this->assertSame(11, $dto->id);
        $this->assertSame(3, $dto->stepNumber);
        $this->assertSame('Дождаться', $dto->name);
        $this->assertSame('Проверка', $dto->description);
        $this->assertSame('Подсказка', $dto->tooltip);
    }

    public function testDefaults(): void
    {
        $dto = HelpStepDto::fromArray(['id' => 1, 'name' => 'X']);
        $this->assertSame(0, $dto->stepNumber);
        $this->assertSame('', $dto->description);
        $this->assertSame('', $dto->tooltip);
    }
}
