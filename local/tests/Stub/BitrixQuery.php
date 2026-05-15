<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

/**
 * Minimal stub for \Bitrix\Main\ORM\Query\Query — chainable, returns empty result.
 * Used in repository unit tests where the entity is missing (iblock not registered).
 */
class BitrixQuery
{
    public function where(...$args): self { return $this; }
    public function whereIn(...$args): self { return $this; }
    public function whereNot(...$args): self { return $this; }
    public function whereNotIn(...$args): self { return $this; }
    public function setSelect(array $select): self { return $this; }
    public function setOrder(array $order): self { return $this; }
    public function setLimit(int $limit): self { return $this; }
    public function setOffset(int $offset): self { return $this; }
    public function setCacheTtl(int $ttl): self { return $this; }
    public function cacheJoins(bool $value): self { return $this; }

    public function exec(): self { return $this; }

    public function fetch(): ?array { return null; }

    /** Iterable interface for foreach — empty by default. */
    public function getIterator(): \Generator
    {
        yield from [];
    }
}
