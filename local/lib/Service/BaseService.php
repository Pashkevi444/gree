<?php

declare(strict_types=1);

namespace Gree\Service;

/**
 * Marker base class for all services. Logging lives in Gree\Logging\FileLogger
 * (singleton). When a service method does IO, wrap the call in try/catch and
 * log a critical entry via `\Gree\Logging\FileLogger::getInstance()->critical(...)`
 * before rethrowing.
 */
abstract class BaseService {}
