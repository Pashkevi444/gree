<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;
use Gree\Contract\Repository\ContactsRepositoryInterface;
use Gree\Service\ContactsService;
use PHPUnit\Framework\TestCase;

final class ContactsServiceTest extends TestCase
{
    private ContactsRepositoryInterface $repo;
    private ContactsService             $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(ContactsRepositoryInterface::class);
        $this->service = new ContactsService($this->repo);
    }

    public function testGetChannelsDelegates(): void
    {
        $c = new ContactChannelCollection();
        $this->repo->expects($this->once())->method('getChannels')->willReturn($c);
        $this->assertSame($c, $this->service->getChannels());
    }

    public function testGetAddressesDelegates(): void
    {
        $c = new ContactAddressCollection();
        $this->repo->expects($this->once())->method('getAddresses')->willReturn($c);
        $this->assertSame($c, $this->service->getAddresses());
    }

    public function testRepositoryExceptionPropagates(): void
    {
        $this->repo->method('getChannels')->willThrowException(new \RuntimeException('boom'));
        $this->expectException(\RuntimeException::class);
        $this->service->getChannels();
    }
}
