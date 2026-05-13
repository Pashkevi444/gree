<?php

declare(strict_types=1);

namespace Gree\Core;

use Illuminate\View\Engines\CompilerEngine;

/**
 * Overrides evaluatePath to avoid ob_start/ob_get_clean wrapping.
 * Bitrix's AddBufferContent cycles output buffers internally; wrapping
 * the template include in another OB layer captures the wrong OB chunk
 * and breaks EndBufferContentMan assembly.
 */
final class BitrixBladeEngine extends CompilerEngine
{
    protected function evaluatePath($__path, $__data): string
    {
        (static function () use ($__path, $__data): void {
            extract($__data, EXTR_SKIP);
            require $__path;
        })();

        return '';
    }
}
