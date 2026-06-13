<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\ServiceFeatureDto;
use PHPUnit\Framework\TestCase;

final class ServiceFeatureDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = ServiceFeatureDto::fromArray([
            'id'        => 5,
            'name'      => 'Гарантия',
            'icon_code' => 'warranty',
        ]);

        $this->assertSame(5, $dto->id);
        $this->assertSame('Гарантия', $dto->name);
        $this->assertSame('warranty', $dto->iconCode);
    }

    public function testDefaults(): void
    {
        $dto = ServiceFeatureDto::fromArray(['id' => 1, 'name' => 'X']);
        $this->assertSame('', $dto->iconCode);
    }
}
