<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Notification;

use Gree\Collection\OrderItemCollection;
use Gree\Contract\Mail\MailerInterface;
use Gree\DTO\CityDto;
use Gree\DTO\OrderCustomerDto;
use Gree\DTO\OrderDeliveryDto;
use Gree\DTO\OrderDto;
use Gree\DTO\OrderItemDto;
use Gree\Enum\Color;
use Gree\Enum\Locale;
use Gree\Enum\OrderStatus;
use Gree\Enum\PaymentMethod;
use Gree\Notification\EmailOrderNotifier;
use PHPUnit\Framework\TestCase;

final class EmailOrderNotifierTest extends TestCase
{
    private function order(): OrderDto
    {
        return new OrderDto(
            publicId:   'abc123def456',
            status:     OrderStatus::New,
            customer:   new OrderCustomerDto(name: 'Иван Петров', phone: '+998901234567', telegram: '@ivan'),
            delivery:   new OrderDeliveryDto(
                city:      new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'),
                street:    'Amir Temur',
                house:     '15',
                apartment: '42',
                comment:   'позвонить заранее',
            ),
            payment:    PaymentMethod::UzumBank,
            items:      new OrderItemCollection(
                new OrderItemDto(
                    offerId:     88,
                    productName: 'Gree BORA X 07',
                    productCode: 'gree-bora-x-07',
                    area:        30,
                    color:       Color::White,
                    quantity:    2,
                    unitPrice:   3_000_000,
                    totalPrice:  6_000_000,
                ),
            ),
            total:      6_000_000,
            itemsCount: 2,
            locale:     Locale::Ru,
            id:         1001,
            createdAt:  '2026-07-02 10:30:00',
        );
    }

    public function testDoesNothingWhenMailerNotConfigured(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->method('isConfigured')->willReturn(false);
        $mailer->expects($this->never())->method('send');

        (new EmailOrderNotifier($mailer, 'https://gree.ru'))->notify($this->order());
    }

    public function testSendsMailWithAllOrderFieldsAndAdminLink(): void
    {
        $subject = '';
        $body = '';
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->method('isConfigured')->willReturn(true);
        $mailer->expects($this->once())
            ->method('send')
            ->willReturnCallback(function (string $s, string $b) use (&$subject, &$body): bool {
                $subject = $s;
                $body = $b;
                return true;
            });

        (new EmailOrderNotifier($mailer, 'https://gree.ru'))->notify($this->order());

        // Тема — номер заказа
        $this->assertStringContainsString('abc123def456', $subject);

        // Тело: сумма / оплата / статус
        $this->assertStringContainsString('6 000 000', $body);
        $this->assertStringContainsString('Рассрочка UZUM', $body);

        // Покупатель
        $this->assertStringContainsString('Иван Петров', $body);
        $this->assertStringContainsString('+998901234567', $body);
        $this->assertStringContainsString('@ivan', $body);

        // Доставка
        $this->assertStringContainsString('Ташкент', $body);
        $this->assertStringContainsString('Amir Temur', $body);
        $this->assertStringContainsString('15', $body);
        $this->assertStringContainsString('42', $body);
        $this->assertStringContainsString('позвонить заранее', $body);

        // Позиции
        $this->assertStringContainsString('Gree BORA X 07', $body);
        $this->assertStringContainsString('Белый', $body);

        // Ссылка на кастомную деталку админки — по ID записи, не publicId
        $this->assertStringContainsString('https://gree.ru/bitrix/admin/gree_orders_view.php?ID=1001', $body);
    }

    public function testSwallowsMailerExceptionSoOrderIsNotBroken(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->method('isConfigured')->willReturn(true);
        $mailer->method('send')->willThrowException(new \RuntimeException('smtp down'));

        // Не должно кинуть — заказ уже создан.
        (new EmailOrderNotifier($mailer, 'https://gree.ru'))->notify($this->order());

        $this->assertTrue(true);
    }

    public function testOmitsAdminLinkWhenBaseUrlEmpty(): void
    {
        $body = '';
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->method('isConfigured')->willReturn(true);
        $mailer->method('send')->willReturnCallback(function (string $s, string $b) use (&$body): bool {
            $body = $b;
            return true;
        });

        (new EmailOrderNotifier($mailer, ''))->notify($this->order());

        $this->assertStringNotContainsString('gree_orders_view.php', $body);
        $this->assertStringContainsString('abc123def456', $body);
    }
}
