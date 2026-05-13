<?php

declare(strict_types=1);

namespace Gree\Core\Cache;

use Bitrix\Main\Data\Cache;
use Gree\Contract\Cache\CacheInterface;

final class BitrixCache implements CacheInterface
{
    public function __construct(
        private readonly Cache $cache,
        private readonly string $basedir = '/gree/',
    ) {}

    public function get(string $key): mixed
    {
        if ($this->cache->initCache(3600, $key, $this->basedir)) {
            return $this->cache->getVars()['v'];
        }

        return null;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $this->cache->startDataCache($ttl, $key, $this->basedir);
        $this->cache->endDataCache(['v' => $value]);
    }

    public function delete(string $key): void
    {
        $this->cache->clean($key, $this->basedir);
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
}
