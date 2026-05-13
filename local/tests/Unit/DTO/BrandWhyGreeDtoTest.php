<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\BrandWhyGreeDto;
use PHPUnit\Framework\TestCase;

final class BrandWhyGreeDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = BrandWhyGreeDto::fromArray([
            'id'          => 2,
            'name'        => 'Почему выбирают Gree',
            'description' => 'Мировой лидер',
            'button_text' => 'Узнать больше',
            'button_url'  => '/brand/gree/',
        ]);

        $this->assertSame(2, $dto->id);
        $this->assertSame('Почему выбирают Gree', $dto->name);
        $this->assertSame('Мировой лидер', $dto->description);
        $this->assertSame('Узнать больше', $dto->buttonText);
        $this->assertSame('/brand/gree/', $dto->buttonUrl);
    }

    public function testButtonDefaultsToEmpty(): void
    {
        $dto = BrandWhyGreeDto::fromArray(['id' => 1, 'name' => 'X', 'description' => 'Y']);

        $this->assertSame('', $dto->buttonText);
        $this->assertSame('', $dto->buttonUrl);
    }
}
