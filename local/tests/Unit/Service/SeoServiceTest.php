<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Contract\Repository\SeoRepositoryInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\DTO\SeoDto;
use Gree\Enum\Locale;
use Gree\Service\SeoService;
use PHPUnit\Framework\TestCase;

final class SeoServiceTest extends TestCase
{
    private SeoRepositoryInterface $repo;
    private LanguageServiceInterface $language;
    private SeoService $service;

    protected function setUp(): void
    {
        $this->repo     = $this->createMock(SeoRepositoryInterface::class);
        $this->language = $this->createMock(LanguageServiceInterface::class);
        $this->language->method('get')->willReturn(Locale::Ru);

        $this->service = new SeoService($this->repo, $this->language);
    }

    public function testForPageReturnsRepositoryDto(): void
    {
        $dto = new SeoDto(title: 'Test', description: 'D');
        $this->repo->expects($this->once())
            ->method('findByPageCode')
            ->with('home', Locale::Ru)
            ->willReturn($dto);

        $this->assertSame($dto, $this->service->forPage('home'));
    }

    public function testForPageReturnsEmptyDtoOnMiss(): void
    {
        $this->repo->method('findByPageCode')->willReturn(null);

        $result = $this->service->forPage('nonexistent');

        $this->assertSame('', $result->title);
        $this->assertSame('', $result->description);
    }

    public function testForPageSwallowsRepositoryFailures(): void
    {
        $this->repo->method('findByPageCode')->willThrowException(new \RuntimeException('boom'));

        $result = $this->service->forPage('home');

        // Fail-soft: layout must keep rendering even if SEO storage is broken.
        $this->assertSame('', $result->title);
    }
}
