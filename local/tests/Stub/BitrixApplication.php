<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

class BitrixApplication
{
    private static self $instance;
    private BitrixSession $session;
    private BitrixRouter $router;

    private function __construct()
    {
        $this->session = new BitrixSession();
        $this->router = new BitrixRouter();
    }

    public static function getInstance(): static
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function resetInstance(): void
    {
        self::$instance = new static();
    }

    public function getSession(): BitrixSession
    {
        return $this->session;
    }

    public function getRouter(): BitrixRouter
    {
        return $this->router;
    }
}
