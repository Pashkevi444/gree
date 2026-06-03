<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\ContactChannelDto;
use PHPUnit\Framework\TestCase;

final class ContactChannelDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = ContactChannelDto::fromArray([
            'id'           => 1,
            'name'         => 'Офис',
            'description'  => '+998 71 500 00 00',
            'button_label' => 'Показать',
            'button_url'   => '',
            'icon_code'    => 'office',
            'latitude'     => '41.29',
            'longitude'    => '69.23',
        ]);

        $this->assertSame(1, $dto->id);
        $this->assertSame('Офис', $dto->name);
        $this->assertSame('+998 71 500 00 00', $dto->description);
        $this->assertSame('Показать', $dto->buttonLabel);
        $this->assertSame('', $dto->buttonUrl);
        $this->assertSame('office', $dto->iconCode);
        $this->assertSame('41.29', $dto->latitude);
        $this->assertSame('69.23', $dto->longitude);
    }

    public function testOpensMapWhenUrlEmptyAndCoordsPresent(): void
    {
        $dto = ContactChannelDto::fromArray([
            'id' => 1, 'name' => 'X', 'button_url' => '',
            'latitude' => '41.29', 'longitude' => '69.23',
        ]);
        $this->assertTrue($dto->opensMap());
    }

    public function testOpensMapFalseWhenUrlPresent(): void
    {
        $dto = ContactChannelDto::fromArray([
            'id' => 1, 'name' => 'X', 'button_url' => 'https://t.me/x',
            'latitude' => '41.29', 'longitude' => '69.23',
        ]);
        $this->assertFalse($dto->opensMap());
    }

    public function testOpensMapFalseWhenCoordsMissing(): void
    {
        $dto = ContactChannelDto::fromArray([
            'id' => 1, 'name' => 'X', 'button_url' => '',
            'latitude' => '', 'longitude' => '',
        ]);
        $this->assertFalse($dto->opensMap());
    }
}
