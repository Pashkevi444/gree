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
        $this->assertSame('en', Locale::En->value);
    }

    public function testDefaultIsRu(): void
    {
        $this->assertSame(Locale::Ru, Locale::default());
    }

    public function testTryFromInvalidReturnsNull(): void
    {
        $this->assertNull(Locale::tryFrom('de'));
    }

    public function testCasesCount(): void
    {
        $this->assertCount(2, Locale::cases());
    }

    public function testFromAcceptLanguageReturnsRuForCisLanguage(): void
    {
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('ru-RU,ru;q=0.9,en;q=0.8'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('uk,en;q=0.7'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('uz-UZ,uz;q=0.9'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('be-BY'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('kk'));
        $this->assertSame(Locale::Ru, Locale::fromAcceptLanguage('ky'));
    }

    public function testFromAcceptLanguageReturnsEnForNonCisLanguage(): void
    {
        $this->assertSame(Locale::En, Locale::fromAcceptLanguage('en-US,en;q=0.9'));
        $this->assertSame(Locale::En, Locale::fromAcceptLanguage('de-DE,de;q=0.9,en;q=0.5'));
        $this->assertSame(Locale::En, Locale::fromAcceptLanguage('fr-FR'));
        $this->assertSame(Locale::En, Locale::fromAcceptLanguage('zh-CN'));
    }

    public function testFromAcceptLanguageReturnsEnForEmptyHeader(): void
    {
        $this->assertSame(Locale::En, Locale::fromAcceptLanguage(''));
        $this->assertSame(Locale::En, Locale::fromAcceptLanguage(null));
    }
}
