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
            'code'         => 'office',
            'name'         => 'Офис',
            'description'  => 'с 9 до 18',
            'button_label' => 'Показать',
            'button_url'   => '',
            'icon_code'    => 'office',
            'phone'        => '+998 71 500 00 00',
            'latitude'     => '41.29',
            'longitude'    => '69.23',
        ]);

        $this->assertSame(1, $dto->id);
        $this->assertSame('office', $dto->code);
        $this->assertSame('Офис', $dto->name);
        $this->assertSame('с 9 до 18', $dto->description);
        $this->assertSame('Показать', $dto->buttonLabel);
        $this->assertSame('', $dto->buttonUrl);
        $this->assertSame('office', $dto->iconCode);
        $this->assertSame('+998 71 500 00 00', $dto->phone);
        $this->assertSame('41.29', $dto->latitude);
        $this->assertSame('69.23', $dto->longitude);
    }

    public function testEmailAddressStripsMailtoPrefix(): void
    {
        $dto = ContactChannelDto::fromArray(['button_url' => 'mailto:foo@bar.uz']);
        $this->assertSame('foo@bar.uz', $dto->emailAddress());
    }

    public function testEmailAddressNullForNonMailto(): void
    {
        $this->assertNull(ContactChannelDto::fromArray(['button_url' => 'https://t.me/x'])->emailAddress());
        $this->assertNull(ContactChannelDto::fromArray(['button_url' => ''])->emailAddress());
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
