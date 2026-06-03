<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;
use Gree\Contract\Repository\ContactsRepositoryInterface;
use Gree\DTO\ContactChannelDto;
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

    public function testFindChannelByCodeDelegates(): void
    {
        $dto = new ContactChannelDto(id: 1, code: 'office', name: 'Офис', phone: '+998');
        $this->repo->expects($this->once())->method('findChannelByCode')->with('office')->willReturn($dto);
        $this->assertSame($dto, $this->service->findChannelByCode('office'));
    }

    public function testFindChannelByCodeReturnsNullWhenMissing(): void
    {
        $this->repo->method('findChannelByCode')->willReturn(null);
        $this->assertNull($this->service->findChannelByCode('unknown'));
    }

    public function testFindChannelByCodeFailSoftSwallowsException(): void
    {
        // Fail-soft: вызов идёт из шапки/футера, не должен валить страницу.
        $this->repo->method('findChannelByCode')->willThrowException(new \RuntimeException('boom'));
        $this->assertNull($this->service->findChannelByCode('office'));
    }
}
