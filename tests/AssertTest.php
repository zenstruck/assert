<?php

namespace Zenstruck\Assert\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Tests\Fixture\TraceableHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AssertTest extends TestCase
{
    use ResetHandler;

    /** @var TraceableHandler */
    private $handler;

    protected function setUp(): void
    {
        Assert::useHandler($this->handler = new TraceableHandler());
    }

    /**
     * @test
     */
    public function that_success(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::that(function() {});
        Assert::that(function() { return 'value'; });

        $this->assertSame(2, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());
    }

    /**
     * @test
     */
    public function that_failure(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::that(function() { AssertionFailed::throw('message'); });

        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(1, $this->handler->failures());
        $this->assertSame('message', $this->handler->lastFailure()->getMessage());
    }

    /**
     * @test
     */
    public function true_success(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::true(true, 'message1');
        Assert::true(\is_string('string'), 'message2');

        $this->assertSame(2, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());
    }

    /**
     * @test
     */
    public function true_failure(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::true(false, 'message1');
        $this->assertSame('message1', $this->handler->lastFailure()->getMessage());

        Assert::true(\is_string(5), 'message2 with %s', ['context']);
        $this->assertSame('message2 with context', $this->handler->lastFailure()->getMessage());

        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(2, $this->handler->failures());
    }

    /**
     * @test
     */
    public function false_success(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::false(false, 'message1');
        Assert::false(\is_string(5), 'message2');

        $this->assertSame(2, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());
    }

    /**
     * @test
     */
    public function false_failure(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::false(true, 'message1');
        $this->assertSame('message1', $this->handler->lastFailure()->getMessage());

        Assert::false(\is_string('string'), 'message2 with %s', ['context']);
        $this->assertSame('message2 with context', $this->handler->lastFailure()->getMessage());

        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(2, $this->handler->failures());
    }

    /**
     * @test
     */
    public function generic_fail(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::fail('message1');
        $this->assertSame('message1', $this->handler->lastFailure()->getMessage());

        Assert::fail('message2 with %s', ['context']);
        $this->assertSame('message2 with context', $this->handler->lastFailure()->getMessage());

        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(2, $this->handler->failures());
    }

    /**
     * @test
     */
    public function throws_success_for_class_name(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::throws(\RuntimeException::class, function() { throw new \RuntimeException(); });
        Assert::throws(\Throwable::class, function() { throw new \RuntimeException(); });

        $this->assertSame(2, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());
    }

    /**
     * @test
     */
    public function throws_success_for_callable(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::throws(function(\RuntimeException $e) {}, function() { throw new \RuntimeException(); });

        $expectedException = new \RuntimeException();
        $actualException = null;

        Assert::throws(
            function(\Throwable $e) use (&$actualException) {
                $actualException = $e;
            },
            function() use ($expectedException) {
                throw $expectedException;
            }
        );

        $this->assertSame($expectedException, $actualException);

        $this->assertSame(2, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());
    }

    /**
     * @test
     */
    public function throws_failure_if_no_throw(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::throws(\RuntimeException::class, function() {});

        $this->assertSame('No exception thrown. Expected "RuntimeException".', $this->handler->lastFailure()->getMessage());

        $actualException = null;

        Assert::throws(
            function(\Throwable $e) use (&$actualException) {
                $actualException = $e;
            },
            function() {}
        );

        $this->assertSame('No exception thrown. Expected "Throwable".', $this->handler->lastFailure()->getMessage());
        $this->assertNull($actualException);

        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(2, $this->handler->failures());
    }

    /**
     * @test
     */
    public function throws_failure_if_mismatch(): void
    {
        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(0, $this->handler->failures());

        Assert::throws(\RuntimeException::class, function() { throw new \Exception(); });

        $this->assertSame('Expected "RuntimeException" to be thrown but got "Exception".', $this->handler->lastFailure()->getMessage());

        $actualException = null;

        Assert::throws(
            function(\InvalidArgumentException $e) use (&$actualException) {
                $actualException = $e;
            },
            function() {
                throw new \RuntimeException();
            }
        );

        $this->assertSame('Expected "InvalidArgumentException" to be thrown but got "RuntimeException".', $this->handler->lastFailure()->getMessage());
        $this->assertNull($actualException);

        $this->assertSame(0, $this->handler->successCount());
        $this->assertCount(2, $this->handler->failures());
    }
}
