<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Contract\Service\TranslationLoaderInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\Enum\Locale;
use Gree\Logging\FileLogger;

final class TranslatorService extends BaseService implements TranslatorServiceInterface
{
    /** @var array<string, array{ru: string, uz: string}>|null */
    private ?array $cache = null;

    public function __construct(private readonly TranslationLoaderInterface $loader) {}

    public function translate(string $code, Locale $locale, array $params = []): string
    {
        // Fail-soft: a broken HL/translation store must not break the page.
        // Return the code itself so layouts keep rendering.
        try {
            $entries = $this->cache ??= $this->loader->all();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed to load translations', [
                'code' => $code,
                'exception' => $e,
            ]);
            return $this->interpolate($code, $params);
        }

        $entry = $entries[$code] ?? null;
        if ($entry === null) {
            return $this->interpolate($code, $params);
        }

        $value = $entry[$locale->value] ?? '';
        if ($value === '' && $locale !== Locale::Ru) {
            $value = $entry['ru'] ?? '';
        }

        if ($value === '') {
            return $this->interpolate($code, $params);
        }

        return $this->interpolate($value, $params);
    }

    /**
     * @param array<string, string|int|float> $params
     */
    private function interpolate(string $text, array $params): string
    {
        if (!$params) {
            return $text;
        }
        $replacements = [];
        foreach ($params as $key => $val) {
            $replacements[':' . $key] = (string) $val;
        }
        return strtr($text, $replacements);
    }
}
