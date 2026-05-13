<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

class BitrixApplication
{
    private static self $instance;
    private BitrixSession $session;

    private function __construct()
    {
        $this->session = new BitrixSession();
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
}
