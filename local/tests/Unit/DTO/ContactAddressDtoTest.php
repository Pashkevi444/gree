<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\ContactAddressDto;
use PHPUnit\Framework\TestCase;

final class ContactAddressDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = ContactAddressDto::fromArray([
            'id'        => 9,
            'name'      => 'Махтумкули, 119',
            'schedule'  => '9–18',
            'phones'    => ['+998 71 500 00 00', '+998 91 809 09 09'],
            'image_url' => '/upload/a.png',
            'latitude'  => '41.29',
            'longitude' => '69.23',
        ]);

        $this->assertSame(9, $dto->id);
        $this->assertSame('Махтумкули, 119', $dto->name);
        $this->assertSame('9–18', $dto->schedule);
        $this->assertSame(['+998 71 500 00 00', '+998 91 809 09 09'], $dto->phones);
        $this->assertSame('/upload/a.png', $dto->imageUrl);
        $this->assertSame('41.29', $dto->latitude);
        $this->assertSame('69.23', $dto->longitude);
    }

    public function testPhonesDefaultsToEmptyArray(): void
    {
        $dto = ContactAddressDto::fromArray(['id' => 1, 'name' => 'X']);
        $this->assertSame([], $dto->phones);
    }
}
