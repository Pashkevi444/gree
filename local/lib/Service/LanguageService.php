<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Enum\Locale;
use Gree\Logging\FileLogger;

final class LanguageService extends BaseService implements LanguageServiceInterface
{
    private const SESSION_KEY = 'locale';

    public function get(): Locale
    {
        // Plain session read — cannot meaningfully throw, called many times per page.
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $raw = $session->get(self::SESSION_KEY);

        return is_string($raw) ? (Locale::tryFrom($raw) ?? Locale::default()) : Locale::default();
    }

    public function set(Locale $locale): void
    {
        try {
            \Bitrix\Main\Application::getInstance()
                ->getSession()
                ->set(self::SESSION_KEY, $locale->value);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'locale' => $locale->value,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    public function detectAndStore(\Bitrix\Main\HttpRequest $request): Locale
    {
        try {
            $session = \Bitrix\Main\Application::getInstance()->getSession();
            if ($session->has(self::SESSION_KEY)) {
                return $this->get();
            }

            $locale = Locale::fromAcceptLanguage($request->getHeader('Accept-Language'));
            $this->set($locale);

            return $locale;
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
