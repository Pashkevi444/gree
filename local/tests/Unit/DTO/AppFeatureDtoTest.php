<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\AppFeatureDto;
use PHPUnit\Framework\TestCase;

final class AppFeatureDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = AppFeatureDto::fromArray([
            'id'          => '1',
            'name'        => 'Контроль',
            'description' => 'Из любой точки',
            'icon_code'   => 'remote',
        ]);

        $this->assertSame(1,              $dto->id);
        $this->assertSame('Контроль',     $dto->name);
        $this->assertSame('Из любой точки', $dto->description);
        $this->assertSame('remote',       $dto->iconCode);
    }

    public function testFromArrayDefaults(): void
    {
        $dto = AppFeatureDto::fromArray(['id' => '1', 'name' => 'X']);

        $this->assertSame('', $dto->description);
        $this->assertSame('', $dto->iconCode);
    }
}
