<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Cache;

use Gree\Core\Cache\BitrixCache;
use PHPUnit\Framework\TestCase;

final class BitrixCacheTest extends TestCase
{
    private BitrixCache $cache;
    private \Bitrix\Main\Data\Cache $bitrixCache;

    protected function setUp(): void
    {
        $this->bitrixCache = $this->createMock(\Bitrix\Main\Data\Cache::class);
        $this->cache = new BitrixCache($this->bitrixCache, '/gree/products/');
    }

    public function testGetReturnsValueOnCacheHit(): void
    {
        $this->bitrixCache
            ->method('initCache')
            ->with(3600, 'some_key', '/gree/products/')
            ->willReturn(true);

        $this->bitrixCache
            ->method('getVars')
            ->willReturn(['v' => 'cached_value']);

        $this->assertSame('cached_value', $this->cache->get('some_key'));
    }

    public function testGetReturnsNullOnCacheMiss(): void
    {
        $this->bitrixCache
            ->method('initCache')
            ->willReturn(false);

        $this->assertNull($this->cache->get('missing_key'));
    }

    public function testSetWritesToCache(): void
    {
        $this->bitrixCache
            ->expects($this->once())
            ->method('startDataCache')
            ->with(3600, 'some_key', '/gree/products/');

        $this->bitrixCache
            ->expects($this->once())
            ->method('endDataCache')
            ->with(['v' => 'some_value']);

        $this->cache->set('some_key', 'some_value', 3600);
    }

    public function testDeleteCleansCache(): void
    {
        $this->bitrixCache
            ->expects($this->once())
            ->method('clean')
            ->with('some_key', '/gree/products/');

        $this->cache->delete('some_key');
    }

    public function testHasReturnsTrueOnHit(): void
    {
        $this->bitrixCache
            ->method('initCache')
            ->willReturn(true);

        $this->bitrixCache
            ->method('getVars')
            ->willReturn(['v' => 'anything']);

        $this->assertTrue($this->cache->has('some_key'));
    }

    public function testHasReturnsFalseOnMiss(): void
    {
        $this->bitrixCache
            ->method('initCache')
            ->willReturn(false);

        $this->assertFalse($this->cache->has('missing_key'));
    }
}
