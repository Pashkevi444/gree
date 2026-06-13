<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\HowItWorksCardDto;
use PHPUnit\Framework\TestCase;

final class HowItWorksCardDtoTest extends TestCase
{
    public function testFromArrayAndHasLink(): void
    {
        $dto = HowItWorksCardDto::fromArray([
            'id'          => 1,
            'name'        => 'Гибкие условия',
            'description' => 'Разные схемы',
            'link_label'  => 'В телеграм',
            'link_url'    => 'https://t.me/x',
        ]);

        $this->assertSame('Гибкие условия', $dto->name);
        $this->assertSame('Разные схемы', $dto->description);
        $this->assertSame('В телеграм', $dto->linkLabel);
        $this->assertSame('https://t.me/x', $dto->linkUrl);
        $this->assertTrue($dto->hasLink());
    }

    public function testHasLinkFalseWhenUrlEmpty(): void
    {
        $dto = HowItWorksCardDto::fromArray(['id' => 1, 'name' => 'X', 'link_label' => 'L', 'link_url' => '']);
        $this->assertFalse($dto->hasLink());
    }

    public function testHasLinkFalseWhenLabelEmpty(): void
    {
        $dto = HowItWorksCardDto::fromArray(['id' => 1, 'name' => 'X', 'link_label' => '', 'link_url' => '/x']);
        $this->assertFalse($dto->hasLink());
    }
}
