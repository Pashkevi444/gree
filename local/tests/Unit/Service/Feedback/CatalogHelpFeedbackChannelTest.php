<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service\Feedback;

use Gree\Enum\HlblockCode;
use Gree\Service\Feedback\Channel\CatalogHelpFeedbackChannel;
use PHPUnit\Framework\TestCase;

final class CatalogHelpFeedbackChannelTest extends TestCase
{
    private CatalogHelpFeedbackChannel $channel;

    protected function setUp(): void
    {
        $this->channel = new CatalogHelpFeedbackChannel();
    }

    public function testId(): void
    {
        $this->assertSame('catalog-help', $this->channel->id());
    }

    public function testHlblock(): void
    {
        $this->assertSame(HlblockCode::CatalogHelpFeedback, $this->channel->hlblock());
    }

    public function testAllowedAndRequiredFields(): void
    {
        $this->assertSame(['name', 'phone'], $this->channel->allowedFields());
        $this->assertSame(['name', 'phone'], $this->channel->requiredFields());
    }

    public function testMapToRowTrimsAndPrefixesUf(): void
    {
        $row = $this->channel->mapToRow(['name' => '  Иван ', 'phone' => '  +998 71 500 ']);

        $this->assertSame(['UF_NAME' => 'Иван', 'UF_PHONE' => '+998 71 500'], $row);
    }
}
