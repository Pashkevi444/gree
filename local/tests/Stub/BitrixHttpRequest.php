<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

class BitrixHttpRequest
{
    public function __construct(
        private array $query = [],
        private array $post = [],
        private string $method = 'GET',
        private array $headers = [],
    ) {}

    public function getQueryList(): BitrixParameterDictionary
    {
        return new BitrixParameterDictionary($this->query);
    }

    public function getPostList(): BitrixParameterDictionary
    {
        return new BitrixParameterDictionary($this->post);
    }

    public function isPost(): bool
    {
        return strtoupper($this->method) === 'POST';
    }

    public function getRequestMethod(): string
    {
        return $this->method;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }
}
