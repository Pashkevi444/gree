<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

interface CartTokenServiceInterface
{
    /**
     * Read cart token from the cookie store, or null if absent.
     */
    public function read(): ?string;

    /**
     * Generate a new UUID v4 token, persist it as a long-lived HttpOnly cookie,
     * and return it.
     */
    public function issue(): string;
}
