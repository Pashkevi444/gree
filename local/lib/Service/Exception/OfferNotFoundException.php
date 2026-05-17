<?php

declare(strict_types=1);

namespace Gree\Service\Exception;

/**
 * Thrown when the cart layer is asked to add an offer that doesn't exist or
 * isn't active. The controller maps this to HTTP 422.
 */
final class OfferNotFoundException extends \RuntimeException {}
