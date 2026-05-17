<?php

declare(strict_types=1);

namespace Gree\Security;

/**
 * Thrown by ApiGuard when a request fails CSRF or same-origin checks.
 *
 * HTTP status 403 — semantically "forbidden". Don't use 419 (laravel-style
 * "page expired"), it's not standard and Bitrix doesn't know it.
 */
final class AccessDeniedException extends BaseSecurityException {}
