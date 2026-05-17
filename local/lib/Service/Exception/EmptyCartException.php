<?php

declare(strict_types=1);

namespace Gree\Service\Exception;

/**
 * Thrown when checkout is attempted with no cart or zero items. Controller
 * maps to HTTP 422 with a meaningful body.
 */
final class EmptyCartException extends BaseServiceException {}
