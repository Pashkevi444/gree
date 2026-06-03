<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\PaymentMethodDto;
use PHPUnit\Framework\TestCase;

final class PaymentMethodDtoTest extends TestCase
{
    public function testFromArray(): void
    {
        $dto = PaymentMethodDto::fromArray([
            'id'        => '7',
            'name'      => 'Uzcard',
            'image_url' => '/upload/iblock/a/b.png',
        ]);

        $this->assertSame(7, $dto->id);
        $this->assertSame('Uzcard', $dto->name);
        $this->assertSame('/upload/iblock/a/b.png', $dto->imageUrl);
    }

    public function testDefaults(): void
    {
        $dto = PaymentMethodDto::fromArray(['id' => 1, 'name' => 'X']);
        $this->assertSame('', $dto->imageUrl);
    }
}
