<?php

/*
 * This file is part of the zenstruck/assert package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        Assert::run(fn() => 'value');

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

        Assert::true(false, 'message2 with %s', ['context']);
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
        Assert::false(false, 'message2');

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

        try {
            Assert::fail('message1');
        } catch (AssertionFailed $e) {
        }

        $this->assertSame('message1', $this->handler->lastFailureMessage());

        try {
            Assert::fail('message2 with %s', ['context']);
        } catch (AssertionFailed $e) {
        }

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

    /**
     * @test
     */
    public function try_success(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        $value = Assert::try(fn() => 'value');

        $this->assertSame(1, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());
        $this->assertSame('value', $value);
    }

    /**
     * @test
     */
    public function try_failure(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(0, $this->handler->failureCount());

        try {
            Assert::try(function() { throw new \RuntimeException('exception message'); });
        } catch (AssertionFailed $e) {
            $this->assertSame('exception message', $e->getMessage());
        }

        try {
            Assert::try(function() { throw new \RuntimeException('exception message'); }, 'override message');
        } catch (AssertionFailed $e) {
            $this->assertSame('override message', $e->getMessage());
        }

        try {
            Assert::try(function() { throw new \RuntimeException('exception message'); }, 'override message {context} {exception} {message}', ['context' => 'value']);
        } catch (AssertionFailed $e) {
            $this->assertSame('override message value RuntimeException exception message', $e->getMessage());
        }

        $this->assertSame(0, $this->handler->successCount());
        $this->assertSame(3, $this->handler->failureCount());
        $this->assertInstanceOf(\RuntimeException::class, $this->handler->failures()[0]->getPrevious());
        $this->assertInstanceOf(\RuntimeException::class, $this->handler->failures()[1]->getPrevious());
        $this->assertInstanceOf(\RuntimeException::class, $this->handler->failures()[2]->getPrevious());
        $this->assertSame('exception message', $this->handler->failures()[0]->getMessage());
        $this->assertSame('override message', $this->handler->failures()[1]->getMessage());
        $this->assertSame('override message value RuntimeException exception message', $this->handler->failures()[2]->getMessage());
    }

    /**
     * @test
     */
    public function html(): void
    {
        $this->assertSame(0, $this->handler->successCount());

        Assert::html('<html><body><h1>hello</h1></body></html>')
            ->contains('hello')
            ->containsIn('h1', 'hello')
        ;

        $this->assertSame(2, $this->handler->successCount());
    }
}
