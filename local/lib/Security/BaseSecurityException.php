<?php

declare(strict_types=1);

namespace Gree\Security;

/**
 * Маркер-база для исключений безопасности (Access denied, CSRF mismatch и
 * прочие отказы пред-контроллерных гардов). Контроллер ловит этот базовый
 * тип и мапит в 403 единообразно — каждое конкретное исключение перечислять
 * необязательно.
 */
abstract class BaseSecurityException extends \RuntimeException {}
