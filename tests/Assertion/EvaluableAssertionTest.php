<?php

namespace Zenstruck\Assert\Tests\Assertion;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Tests\HasTraceableHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class EvaluableAssertionTest extends TestCase
{
    use HasTraceableHandler;

    /**
     * @test
     * @dataProvider successProvider
     */
    public function success(): void
    {
        Assert::{$this->assertMethod()}(...\func_get_args());

        $this->assertSame(1, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());
    }

    /**
     * @test
     * @dataProvider failureProvider
     */
    public function failure(): void
    {
        $args = \func_get_args();
        $expectedMessage = \array_shift($args);

        Assert::{$this->assertMethod()}(...$args);

        $this->assertSame(1, $this->handler->failureCount());
        $this->assertSame($expectedMessage, $this->handler->lastFailureMessage());
        $this->assertSame(0, $this->handler->successCount());
    }

    /**
     * @test
     * @dataProvider notSuccessProvider
     */
    public function not_success(): void
    {
        Assert::{$this->notAssertMethod()}(...\func_get_args());

        $this->assertSame(1, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());
    }

    /**
     * @test
     * @dataProvider notFailureProvider
     */
    public function not_failure(): void
    {
        $args = \func_get_args();
        $expectedMessage = \array_shift($args);

        Assert::{$this->notAssertMethod()}(...$args);

        $this->assertSame(1, $this->handler->failureCount());
        $this->assertSame($expectedMessage, $this->handler->lastFailureMessage());
        $this->assertSame(0, $this->handler->successCount());
    }

    abstract public static function successProvider(): iterable;

    abstract public static function failureProvider(): iterable;

    abstract public static function notSuccessProvider(): iterable;

    abstract public static function notFailureProvider(): iterable;

    abstract protected function assertMethod(): string;

    abstract protected function notAssertMethod(): string;
}
