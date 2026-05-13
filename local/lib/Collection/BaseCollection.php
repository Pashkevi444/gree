<?php

declare(strict_types=1);

namespace Gree\Collection;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @template T of object
 * @extends ArrayCollection<int, T>
 */
abstract class BaseCollection extends ArrayCollection
{
    /** @return class-string<T> */
    abstract protected function itemClass(): string;

    protected function createFrom(array $elements): static
    {
        return new static(...array_values($elements));
    }

    public function add(mixed $element): void
    {
        $class = $this->itemClass();
        if (!$element instanceof $class) {
            throw new \InvalidArgumentException(
                sprintf('Expected %s, got %s.', $class, get_debug_type($element))
            );
        }

        parent::add($element);
    }
}
