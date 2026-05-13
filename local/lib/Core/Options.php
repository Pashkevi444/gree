<?php

declare(strict_types=1);

namespace Gree\Core;

use Bitrix\Main\Config\Option;

final class Options
{
    private static ?self $instance = null;

    private function __construct(
        public readonly string $phone = '',
        public readonly string $email = '',
        public readonly string $address = '',
        public readonly string $tgLink = '',
        public readonly string $vkLink = '',
    ) {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            $opt = Option::getForModule('gree.core');

            self::$instance = new self(
                phone: $opt['PHONE'] ?? '',
                email: $opt['EMAIL'] ?? '',
                address: $opt['ADDRESS'] ?? '',
                tgLink: $opt['TG_LINK'] ?? '',
                vkLink: $opt['VK_LINK'] ?? '',
            );
        }

        return self::$instance;
    }

    /** Reset cached instance (useful in tests or after option save). */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
