<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

interface TranslationLoaderInterface
{
    /**
     * @return array<string, array{ru: string, en: string}> code → {ru, en}
     */
    public function all(): array;
}
