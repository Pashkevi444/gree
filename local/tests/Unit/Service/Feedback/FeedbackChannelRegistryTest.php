<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service\Feedback;

use Gree\Contract\Feedback\FeedbackChannelInterface;
use Gree\Enum\HlblockCode;
use Gree\Service\Feedback\FeedbackChannelRegistry;
use PHPUnit\Framework\TestCase;

final class FeedbackChannelRegistryTest extends TestCase
{
    public function testEmptyRegistryReturnsNull(): void
    {
        $this->assertNull((new FeedbackChannelRegistry())->get('anything'));
        $this->assertSame([], (new FeedbackChannelRegistry())->ids());
    }

    public function testConstructorRegistersIterable(): void
    {
        $a = $this->makeChannel('a');
        $b = $this->makeChannel('b');
        $reg = new FeedbackChannelRegistry([$a, $b]);

        $this->assertSame($a, $reg->get('a'));
        $this->assertSame($b, $reg->get('b'));
        $this->assertNull($reg->get('z'));
        $this->assertSame(['a', 'b'], $reg->ids());
    }

    public function testRegisterAddsChannel(): void
    {
        $reg = new FeedbackChannelRegistry();
        $reg->register($this->makeChannel('catalog-help'));
        $this->assertNotNull($reg->get('catalog-help'));
    }

    public function testDuplicateRegistrationThrows(): void
    {
        $reg = new FeedbackChannelRegistry();
        $reg->register($this->makeChannel('x'));
        $this->expectException(\LogicException::class);
        $reg->register($this->makeChannel('x'));
    }

    private function makeChannel(string $id): FeedbackChannelInterface
    {
        return new class($id) implements FeedbackChannelInterface {
            public function __construct(private readonly string $id) {}
            public function id(): string { return $this->id; }
            public function hlblock(): HlblockCode { return HlblockCode::CatalogHelpFeedback; }
            public function allowedFields(): array { return []; }
            public function requiredFields(): array { return []; }
            public function mapToRow(array $input): array { return []; }
        };
    }
}
