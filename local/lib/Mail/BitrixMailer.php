<?php

declare(strict_types=1);

namespace Gree\Mail;

use Bitrix\Main\Mail\Mail;
use Gree\Contract\Mail\MailerInterface;
use Gree\Core\Env;
use Gree\Logging\FileLogger;

/**
 * Отправка HTML-письма через Bitrix `Main\Mail\Mail::send()`.
 *
 * Получатели и From читаются из .env (`ORDER_MAIL_TO`, `ORDER_MAIL_FROM`).
 * Пустой `ORDER_MAIL_TO` → isConfigured() = false → отправка выключена
 * (локалка / тесты не шлют почту).
 */
final class BitrixMailer implements MailerInterface
{
    /**
     * @param list<string> $recipients
     */
    public function __construct(
        private readonly array $recipients,
        private readonly string $from = '',
    ) {}

    public static function fromEnv(): self
    {
        $raw = (string) Env::get('ORDER_MAIL_TO', '');
        $recipients = array_values(array_filter(array_map('trim', explode(',', $raw))));

        return new self(
            recipients: $recipients,
            from:       (string) Env::get('ORDER_MAIL_FROM', ''),
        );
    }

    public function isConfigured(): bool
    {
        return $this->recipients !== [];
    }

    public function send(string $subject, string $htmlBody): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $header = ['Content-Type' => 'text/html; charset=utf-8'];
        if ($this->from !== '') {
            $header['From'] = $this->from;
        }

        $ok = true;
        foreach ($this->recipients as $to) {
            $sent = Mail::send([
                'TO'           => $to,
                'SUBJECT'      => $subject,
                'BODY'         => $htmlBody,
                'CHARSET'      => 'utf-8',
                'CONTENT_TYPE' => 'html',
                'HEADER'       => $header,
            ]);

            if (!$sent) {
                $ok = false;
                FileLogger::getInstance()->critical('Order e-mail send failed', [
                    'to'      => $to,
                    'subject' => $subject,
                ]);
            }
        }

        return $ok;
    }
}
