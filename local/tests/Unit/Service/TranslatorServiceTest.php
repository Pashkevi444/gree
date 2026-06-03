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
            'header.catalog' => ['ru' => 'Каталог', 'uz' => 'Katalog'],
        ]);

        $this->assertSame('Каталог', $translator->translate('header.catalog', Locale::Ru));
    }

    public function testReturnsUzValueForUzLocale(): void
    {
        $translator = $this->buildTranslator([
            'header.catalog' => ['ru' => 'Каталог', 'uz' => 'Katalog'],
        ]);

        $this->assertSame('Katalog', $translator->translate('header.catalog', Locale::Uz));
    }

    public function testFallsBackToRuWhenUzEmpty(): void
    {
        $translator = $this->buildTranslator([
            'header.catalog' => ['ru' => 'Каталог', 'uz' => ''],
        ]);

        $this->assertSame('Каталог', $translator->translate('header.catalog', Locale::Uz));
    }

    public function testReturnsCodeWhenMissing(): void
    {
        $translator = $this->buildTranslator([]);

        $this->assertSame('header.catalog', $translator->translate('header.catalog', Locale::Uz));
    }

    public function testFallsBackToUzWhenRuEmptyForRuLocale(): void
    {
        // Симметричный fallback: если в предпочтительной локали пусто —
        // тянем из любой другой непустой. Раньше ru→uz не падал — кейс
        // вроде header.lang.uz с пустым RU возвращал сам код.
        $translator = $this->buildTranslator([
            'header.lang.uz' => ['ru' => '', 'uz' => 'O\'zb'],
        ]);
        $this->assertSame('O\'zb', $translator->translate('header.lang.uz', Locale::Ru));
    }

    public function testReturnsCodeWhenAllLocalesEmpty(): void
    {
        $translator = $this->buildTranslator([
            'header.catalog' => ['ru' => '', 'uz' => ''],
        ]);
        $this->assertSame('header.catalog', $translator->translate('header.catalog', Locale::Ru));
    }

    public function testReplacesParameters(): void
    {
        $translator = $this->buildTranslator([
            'catalog.found' => ['ru' => 'Найдено :count моделей', 'uz' => ':count ta model topildi'],
        ]);

        $this->assertSame('12 ta model topildi', $translator->translate('catalog.found', Locale::Uz, ['count' => 12]));
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
