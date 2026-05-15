<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Enum\Locale;
use Gree\Service\TranslatorService;
use PHPUnit\Framework\TestCase;

final class TranslatorServiceTest extends TestCase
{
    public function testReturnsRuValueForRuLocale(): void
    {
        $translator = $this->buildTranslator([
            'header.catalog' => ['ru' => 'Каталог', 'en' => 'Catalog'],
        ]);

        $this->assertSame('Каталог', $translator->translate('header.catalog', Locale::Ru));
    }

    public function testReturnsEnValueForEnLocale(): void
    {
        $translator = $this->buildTranslator([
            'header.catalog' => ['ru' => 'Каталог', 'en' => 'Catalog'],
        ]);

        $this->assertSame('Catalog', $translator->translate('header.catalog', Locale::En));
    }

    public function testFallsBackToRuWhenEnEmpty(): void
    {
        $translator = $this->buildTranslator([
            'header.catalog' => ['ru' => 'Каталог', 'en' => ''],
        ]);

        $this->assertSame('Каталог', $translator->translate('header.catalog', Locale::En));
    }

    public function testReturnsCodeWhenMissing(): void
    {
        $translator = $this->buildTranslator([]);

        $this->assertSame('header.catalog', $translator->translate('header.catalog', Locale::En));
    }

    public function testReplacesParameters(): void
    {
        $translator = $this->buildTranslator([
            'catalog.found' => ['ru' => 'Найдено :count моделей', 'en' => 'Found :count models'],
        ]);

        $this->assertSame('Found 12 models', $translator->translate('catalog.found', Locale::En, ['count' => 12]));
        $this->assertSame('Найдено 12 моделей', $translator->translate('catalog.found', Locale::Ru, ['count' => 12]));
    }

    private function buildTranslator(array $entries): TranslatorService
    {
        $loader = new class($entries) implements \Gree\Contract\Service\TranslationLoaderInterface {
            public function __construct(private array $entries) {}
            public function all(): array
            {
                return $this->entries;
            }
        };

        return new TranslatorService($loader);
    }
}
