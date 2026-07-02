<?php

declare(strict_types=1);

namespace Gree\Notification;

use Gree\Contract\Mail\MailerInterface;
use Gree\Contract\Notification\OrderNotifierInterface;
use Gree\DTO\OrderDto;
use Gree\DTO\OrderItemDto;
use Gree\Logging\FileLogger;

/**
 * Форматирует заказ в HTML-письмо менеджеру и шлёт через MailerInterface.
 *
 * Письмо самодостаточно (что заказали, сумма, контакты, доставка, полный
 * состав) плюс прямая ссылка на кастомную деталку заказа в админке
 * (`/bitrix/admin/gree_orders_view.php?ID=<id>`), чтобы не открывать её ради
 * базовых данных.
 *
 * Fail-soft: любая ошибка отправки логируется critical и гасится — заказ уже
 * создан, письмо best-effort.
 */
final readonly class EmailOrderNotifier implements OrderNotifierInterface
{
    public function __construct(
        private MailerInterface $mailer,
        /** Базовый абсолютный URL сайта для ссылок в админку (пусто → ссылку не добавляем). */
        private string $adminBaseUrl = '',
    ) {}

    public function notify(OrderDto $order): void
    {
        try {
            if (!$this->mailer->isConfigured()) {
                return;
            }
            $this->mailer->send($this->subject($order), $this->body($order));
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'exception' => $e,
                'publicId'  => $order->publicId,
            ]);
            // fail-soft — не роняем оформление заказа из-за письма
        }
    }

    private function subject(OrderDto $order): string
    {
        return 'Новый заказ №' . $order->publicId . ' — ' . $this->money($order->total) . ' UZS';
    }

    private function body(OrderDto $order): string
    {
        $e = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        $rows = '';
        $row = static function (string $k, string $v) use (&$rows, $e): void {
            $rows .= '<tr><td style="padding:2px 12px 2px 0;color:#666">' . $e($k) . '</td>'
                . '<td style="padding:2px 0"><b>' . $v . '</b></td></tr>';
        };

        $row('Сумма', $this->money($order->total) . ' UZS');
        $row('Позиций', $order->itemsCount . ' шт');
        $row('Оплата', $e($order->payment->label()));
        $row('Статус', $e($order->status->label()));
        $row('Локаль', $e($order->locale->value));
        if ($order->createdAt !== '') {
            $row('Создан', $e($order->createdAt));
        }

        $row('Имя', $e($order->customer->name));
        $row('Телефон', $e($order->customer->phone));
        if ($order->customer->telegram !== '') {
            $row('Telegram', $e($order->customer->telegram));
        }

        $cityName = $order->delivery->city->nameRu !== '' ? $order->delivery->city->nameRu : $order->delivery->city->nameUz;
        $row('Город', $e($cityName));
        $row('Улица', $e($order->delivery->street));
        $row('Дом', $e($order->delivery->house));
        if ($order->delivery->apartment !== '') {
            $row('Квартира', $e($order->delivery->apartment));
        }
        if ($order->delivery->comment !== '') {
            $row('Комментарий', $e($order->delivery->comment));
        }

        $items = '';
        foreach ($order->items as $item) {
            $items .= $this->itemRow($item, $e);
        }

        $link = $this->adminLink($order);
        $linkHtml = $link !== ''
            ? '<p style="margin:20px 0"><a href="' . $e($link) . '" style="background:#2f40d5;color:#fff;padding:10px 18px;'
                . 'text-decoration:none;border-radius:6px;display:inline-block">Открыть заказ в админке</a></p>'
            : '';

        return '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111">'
            . '<h2 style="margin:0 0 12px">🛒 Новый заказ №' . $e($order->publicId) . '</h2>'
            . '<table style="border-collapse:collapse;margin-bottom:16px">' . $rows . '</table>'
            . '<h3 style="margin:0 0 8px">Состав заказа</h3>'
            . '<table style="border-collapse:collapse;width:100%;max-width:640px">'
            . '<thead><tr style="text-align:left;border-bottom:1px solid #ddd">'
            . '<th style="padding:6px 8px">Товар</th><th style="padding:6px 8px">Цвет</th>'
            . '<th style="padding:6px 8px">Площадь</th><th style="padding:6px 8px">Кол-во</th>'
            . '<th style="padding:6px 8px">Цена</th><th style="padding:6px 8px">Итого</th></tr></thead>'
            . '<tbody>' . $items . '</tbody></table>'
            . $linkHtml
            . '</div>';
    }

    /** @param callable(string):string $e */
    private function itemRow(OrderItemDto $item, callable $e): string
    {
        return '<tr style="border-bottom:1px solid #f0f0f0">'
            . '<td style="padding:6px 8px">' . $e($item->productName) . '</td>'
            . '<td style="padding:6px 8px">' . ($item->color !== null ? $e($item->color->label()) : '—') . '</td>'
            . '<td style="padding:6px 8px">' . ($item->area > 0 ? $item->area . ' м²' : '—') . '</td>'
            . '<td style="padding:6px 8px">' . $item->quantity . '</td>'
            . '<td style="padding:6px 8px">' . $this->money($item->unitPrice) . ' UZS</td>'
            . '<td style="padding:6px 8px"><b>' . $this->money($item->totalPrice) . ' UZS</b></td>'
            . '</tr>';
    }

    private function adminLink(OrderDto $order): string
    {
        if ($this->adminBaseUrl === '' || $order->id === null) {
            return '';
        }
        return rtrim($this->adminBaseUrl, '/')
            . '/bitrix/admin/gree_orders_view.php?ID=' . $order->id . '&lang=ru';
    }

    private function money(int $value): string
    {
        return number_format($value, 0, '.', ' ');
    }
}
