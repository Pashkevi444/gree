<?php

declare(strict_types=1);

namespace Gree\Contract\Security;

interface ApiGuardInterface
{
    /**
     * Throws AccessDeniedException if the request is a forged cross-site
     * call or fails CSRF token check.
     */
    public function guardStateChanging(object $request): void;
}
