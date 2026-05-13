<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

class BitrixDataCache
{
    public static function createInstance(): static
    {
        return new static();
    }

    public function initCache(int $ttl, string $id, string $basedir = '/'): bool
    {
        return false;
    }

    public function getVars(): array
    {
        return [];
    }

    public function startDataCache(int $ttl, string $id, string $basedir = '/'): bool
    {
        return true;
    }

    public function endDataCache(mixed $vars = false): void {}

    public function clean(string $id, string $basedir = '/'): void {}
}
