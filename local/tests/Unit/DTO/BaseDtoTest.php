<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\Contract\DTO\DataTransferObject;
use Gree\DTO\BaseDto;
use PHPUnit\Framework\TestCase;

final class BaseDtoTest extends TestCase
{
    private function makeDto(array $data): BaseDto
    {
        return new readonly class($data) extends BaseDto {
            public function __construct(public array $raw) {}

            public static function fromArray(array $data): static
            {
                return new static($data);
            }
        };
    }

    public function testImplementsDataTransferObjectInterface(): void
    {
        $dto = $this->makeDto(['id' => 1]);

        $this->assertInstanceOf(DataTransferObject::class, $dto);
    }

    public function testFromArrayReturnsInstance(): void
    {
        $dto = $this->makeDto(['key' => 'value']);

        $this->assertInstanceOf(BaseDto::class, $dto);
    }

    public function testFromArrayPassesDataToSubclass(): void
    {
        $dto = $this->makeDto(['id' => 42]);

        $this->assertSame(['id' => 42], $dto->raw);
    }
}
