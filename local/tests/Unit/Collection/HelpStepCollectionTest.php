<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\HelpStepCollection;
use Gree\DTO\HelpStepDto;
use PHPUnit\Framework\TestCase;

final class HelpStepCollectionTest extends TestCase
{
    private function make(int $step): HelpStepDto
    {
        return new HelpStepDto(id: $step, stepNumber: $step, name: "Step $step");
    }

    public function testEmpty(): void
    {
        $this->assertCount(0, new HelpStepCollection());
    }

    public function testConstructAndAdd(): void
    {
        $c = new HelpStepCollection($this->make(1), $this->make(2));
        $c->add($this->make(3));
        $this->assertCount(3, $c);
    }

    public function testAddInvalidThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new HelpStepCollection())->add('x');
    }
}
