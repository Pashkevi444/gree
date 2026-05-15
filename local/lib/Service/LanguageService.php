<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Enum\Locale;

final class LanguageService extends BaseService implements LanguageServiceInterface
{
    private const SESSION_KEY = 'locale';

    public function get(): Locale
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $raw = $session->get(self::SESSION_KEY);

        return is_string($raw) ? (Locale::tryFrom($raw) ?? Locale::default()) : Locale::default();
    }

    public function set(Locale $locale): void
    {
        \Bitrix\Main\Application::getInstance()
            ->getSession()
            ->set(self::SESSION_KEY, $locale->value);
    }

    public function detectAndStore(\Bitrix\Main\HttpRequest $request): Locale
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        if ($session->has(self::SESSION_KEY)) {
            return $this->get();
        }

        $locale = Locale::fromAcceptLanguage($request->getHeader('Accept-Language'));
        $this->set($locale);

        return $locale;
    }
}
