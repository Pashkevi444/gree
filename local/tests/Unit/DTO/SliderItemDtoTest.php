<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\SliderItemDto;
use PHPUnit\Framework\TestCase;

final class SliderItemDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = SliderItemDto::fromArray([
            'id'              => '3',
            'name'            => 'Заголовок слайда',
            'subtitle'        => 'Подзаголовок <span>+50°C</span>',
            'button_text'     => 'Выбрать',
            'button_url'      => '/catalog/',
            'background_image' => '/upload/slide.png',
        ]);

        $this->assertSame(3,               $dto->id);
        $this->assertSame('Заголовок слайда', $dto->name);
        $this->assertSame('Подзаголовок <span>+50°C</span>', $dto->subtitle);
        $this->assertSame('Выбрать',       $dto->buttonText);
        $this->assertSame('/catalog/',     $dto->buttonUrl);
        $this->assertSame('/upload/slide.png', $dto->backgroundImage);
    }

    public function testFromArrayDefaults(): void
    {
        $dto = SliderItemDto::fromArray(['id' => '1', 'name' => 'X']);

        $this->assertSame('', $dto->subtitle);
        $this->assertSame('', $dto->buttonText);
        $this->assertSame('', $dto->buttonUrl);
        $this->assertSame('', $dto->backgroundImage);
    }
}
