<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Contract\Feedback\FeedbackChannelInterface;
use Gree\Contract\Repository\FeedbackRepositoryInterface;
use Gree\Enum\HlblockCode;
use Gree\Service\Feedback\FeedbackChannelRegistry;
use Gree\Service\FeedbackService;
use PHPUnit\Framework\TestCase;

final class FeedbackServiceTest extends TestCase
{
    public function testUnknownChannelThrowsDomainException(): void
    {
        $service = new FeedbackService(new FeedbackChannelRegistry(), $this->createMock(FeedbackRepositoryInterface::class));
        $this->expectException(\DomainException::class);
        $service->save('does-not-exist', []);
    }

    public function testMissingRequiredFieldThrowsInvalidArg(): void
    {
        $repo = $this->createMock(FeedbackRepositoryInterface::class);
        $repo->expects($this->never())->method('insert');

        $service = new FeedbackService(
            new FeedbackChannelRegistry([$this->channel(required: ['name', 'phone'])]),
            $repo,
        );

        $this->expectException(\InvalidArgumentException::class);
        $service->save('test', ['name' => 'Иван']);  // phone missing
    }

    public function testWhitespaceOnlyValueCountsAsEmpty(): void
    {
        $repo = $this->createMock(FeedbackRepositoryInterface::class);
        $repo->expects($this->never())->method('insert');

        $service = new FeedbackService(
            new FeedbackChannelRegistry([$this->channel(required: ['name'])]),
            $repo,
        );

        $this->expectException(\InvalidArgumentException::class);
        $service->save('test', ['name' => '   ']);
    }

    public function testValidPayloadGoesToChannelAndRepository(): void
    {
        $channel = $this->channel(required: ['name', 'phone']);

        $repo = $this->createMock(FeedbackRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('insert')
            ->with(
                HlblockCode::CatalogHelpFeedback,
                $this->callback(static fn(array $row) => $row === ['UF_NAME' => 'Иван', 'UF_PHONE' => '+998 71'])
            )
            ->willReturn(99);

        $service = new FeedbackService(new FeedbackChannelRegistry([$channel]), $repo);

        $id = $service->save('test', ['name' => '  Иван  ', 'phone' => '+998 71', 'extra' => 'ignored']);
        $this->assertSame(99, $id);
    }

    public function testRepositoryExceptionPropagates(): void
    {
        $repo = $this->createMock(FeedbackRepositoryInterface::class);
        $repo->method('insert')->willThrowException(new \RuntimeException('boom'));

        $service = new FeedbackService(
            new FeedbackChannelRegistry([$this->channel()]),
            $repo,
        );

        $this->expectException(\RuntimeException::class);
        $service->save('test', ['name' => 'X', 'phone' => '+998']);
    }

    /**
     * @param string[] $required
     */
    private function channel(array $required = ['name', 'phone']): FeedbackChannelInterface
    {
        return new class($required) implements FeedbackChannelInterface {
            /** @param string[] $required */
            public function __construct(private readonly array $required) {}
            public function id(): string { return 'test'; }
            public function hlblock(): HlblockCode { return HlblockCode::CatalogHelpFeedback; }
            public function allowedFields(): array { return ['name', 'phone']; }
            public function requiredFields(): array { return $this->required; }
            public function mapToRow(array $input): array
            {
                return ['UF_NAME' => (string) ($input['name'] ?? ''), 'UF_PHONE' => (string) ($input['phone'] ?? '')];
            }
        };
    }
}
