<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

interface TranslationLoaderInterface
{
    /**
     * @return array<string, array{ru: string, uz: string}> code → {ru, uz}
     */
    public function all(): array;
}
