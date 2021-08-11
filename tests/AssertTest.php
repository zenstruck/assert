<?php

namespace Zenstruck\Assert\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Tests\Fixture\NegatableAssertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AssertTest extends TestCase
{
    use HasTraceableHandler;

    /**
     * @test
     */
    public function run_success(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::run(function() {});
        Assert::run(function() { return 'value'; });

        $this->assertSame(2, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());
    }

    /**
     * @test
     */
    public function run_failure(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::run(function() { AssertionFailed::throw('message'); });

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(1, $this->handler->failureCount());
        $this->assertSame('message', $this->handler->lastFailureMessage());
    }

    /**
     * @test
     */
    public function not_success(): void
    {
        $assertion = new NegatableAssertion(true);

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::run($assertion);

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(1, $this->handler->failureCount());
        $this->assertSame('assertion failed', $this->handler->lastFailureMessage());

        Assert::not($assertion);

        $this->assertSame(1, $this->handler->successCount());
        $this->assertSame(1, $this->handler->failureCount());
    }

    /**
     * @test
     */
    public function not_failure(): void
    {
        $assertion = new NegatableAssertion(false);

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::run($assertion);

        $this->assertSame(1, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::not($assertion);

        $this->assertSame(1, $this->handler->successCount());
        $this->assertSame(1, $this->handler->failureCount());
        $this->assertSame('negation failed', $this->handler->lastFailureMessage());
    }

    /**
     * @test
     */
    public function true_success(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::true(true, 'message1');
        Assert::true(\is_string('string'), 'message2');

        $this->assertSame(2, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());
    }

    /**
     * @test
     */
    public function true_failure(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::true(false, 'message1');
        $this->assertSame('message1', $this->handler->lastFailureMessage());

        Assert::true(\is_string(5), 'message2 with %s', ['context']);
        $this->assertSame('message2 with context', $this->handler->lastFailureMessage());

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(2, $this->handler->failureCount());
    }

    /**
     * @test
     */
    public function false_success(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::false(false, 'message1');
        Assert::false(\is_string(5), 'message2');

        $this->assertSame(2, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());
    }

    /**
     * @test
     */
    public function false_failure(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::false(true, 'message1');
        $this->assertSame('message1', $this->handler->lastFailureMessage());

        Assert::false(\is_string('string'), 'message2 with %s', ['context']);
        $this->assertSame('message2 with context', $this->handler->lastFailureMessage());

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(2, $this->handler->failureCount());
    }

    /**
     * @test
     */
    public function generic_fail(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::fail('message1');
        $this->assertSame('message1', $this->handler->lastFailureMessage());

        Assert::fail('message2 with %s', ['context']);
        $this->assertSame('message2 with context', $this->handler->lastFailureMessage());

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(2, $this->handler->failureCount());
    }

    /**
     * @test
     */
    public function generic_pass(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        Assert::pass();

        $this->assertSame(1, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());
    }
}
