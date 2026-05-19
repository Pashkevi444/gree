<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\Locale;
use PHPUnit\Framework\TestCase;

final class LocaleTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertSame('ru', Locale::Ru->value);
        $this->assertSame('uz', Locale::Uz->value);
    }

    public function testDefaultIsRu(): void
    {
        $this->assertSame(Locale::Ru, Locale::default());
    }

    public function testTryFromInvalidReturnsNull(): void
    {
        $this->assertNull(Locale::tryFrom('en'));
        $this->assertNull(Locale::tryFrom('de'));
    }

    public function testCasesCount(): void
    {
        $this->assertCount(2, Locale::cases());
    }

    public function testFromAcceptLanguageReturnsUzForUzbekTag(): void
    {
        $this->assertSame(Locale::Uz, Locale::fromAcceptLanguage('uz-UZ,uz;q=0.9,ru;q=0.8'));
        $this->assertSame(Locale::Uz, Locale::fromAcceptLanguage('uz'));
    }

    public function testFromAcceptLanguageReturnsRuForCisLanguage(): void
    {
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('ru-RU,ru;q=0.9'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('uk'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('be-BY'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('kk'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('ky'));
    }

    public function testFromAcceptLanguageReturnsDefaultForNonCisLanguage(): void
    {
        // English / прочие иностранные → default (Ru), отдельной En-локали больше нет.
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('en-US,en;q=0.9'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('de-DE'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('fr-FR'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('zh-CN'));
    }

    public function testFromAcceptLanguageReturnsDefaultForEmptyHeader(): void
    {
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage(''));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage(null));
    }
}
