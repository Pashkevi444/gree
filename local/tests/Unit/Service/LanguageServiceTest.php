<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Enum\Locale;
use Gree\Service\LanguageService;
use Gree\Tests\Stub\BitrixHttpRequest;
use PHPUnit\Framework\TestCase;

final class LanguageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        \Bitrix\Main\Application::resetInstance();
    }

    public function testGetReturnsLocaleFromSession(): void
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('locale', 'en');

        $service = new LanguageService();

        $this->assertSame(Locale::En, $service->get());
    }

    public function testGetReturnsDefaultWhenSessionEmpty(): void
    {
        $service = new LanguageService();

        $this->assertSame(Locale::Ru, $service->get());
    }

    public function testSetStoresLocaleInSession(): void
    {
        $service = new LanguageService();

        $service->set(Locale::En);

        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $this->assertSame('en', $session->get('locale'));
        $this->assertSame(Locale::En, $service->get());
    }

    public function testDetectAndStoreWritesDetectedLocaleToSession(): void
    {
        $request = new class extends BitrixHttpRequest {
            public function getHeader(string $name): ?string
            {
                return strtolower($name) === 'accept-language' ? 'en-US,en;q=0.9' : null;
            }
        };

        $service = new LanguageService();
        $locale = $service->detectAndStore($request);

        $this->assertSame(Locale::En, $locale);
        $this->assertSame(Locale::En, $service->get());
    }

    public function testDetectAndStoreSkipsWhenSessionAlreadyHasLocale(): void
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('locale', 'ru');

        $request = new class extends BitrixHttpRequest {
            public function getHeader(string $name): ?string
            {
                return strtolower($name) === 'accept-language' ? 'en-US' : null;
            }
        };

        $service = new LanguageService();
        $locale = $service->detectAndStore($request);

        $this->assertSame(Locale::Ru, $locale);
    }
}
