<?php

declare(strict_types=1);

namespace Gree\Contract\Mail;

/**
 * Тонкий транспорт над отправкой почты. Получатель(и) и From — внутри
 * реализации (из .env). Вынесен за интерфейс, чтобы форматтеры (например
 * EmailOrderNotifier) тестировались с моком, без реальной отправки.
 */
interface MailerInterface
{
    /** Получатель(и) заданы — иначе отправка выключена (dev/тесты). */
    public function isConfigured(): bool;

    /** @return bool true, если письмо ушло всем получателям. */
    public function send(string $subject, string $htmlBody): bool;
}
