<?php

declare(strict_types=1);

namespace Gree\Service\Exception;

/**
 * Thrown by OrderService::place() when input or cart state fails validation.
 * Carries a per-field map so the controller can render inline errors.
 */
final class CheckoutValidationException extends BaseServiceException
{
    /**
     * @param array<string, string> $errors  field code → human-readable message
     */
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Checkout validation failed');
    }
}
