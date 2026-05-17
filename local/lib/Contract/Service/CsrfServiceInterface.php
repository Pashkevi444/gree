<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

interface CsrfServiceInterface
{
    public function read(): ?string;

    public function issue(): string;

    public function readOrIssue(): string;

    public function matches(string $sent): bool;
}
