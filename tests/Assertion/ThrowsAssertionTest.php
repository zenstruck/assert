<?php

namespace Zenstruck\Assert\Tests\Assertion;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert\Assertion\ThrowsAssertion;
use Zenstruck\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ThrowsAssertionTest extends TestCase
{
    /**
     * @test
     */
    public function can_customize_the_no_throw_message(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('custom message');

        ThrowsAssertion::expect(\RuntimeException::class, function() {})
            ->ifNotThrown('custom message')
            ->__invoke()
        ;
    }

    /**
     * @test
     */
    public function can_customize_the_no_throw_message_with_default_context(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('custom message RuntimeException');

        ThrowsAssertion::expect(\RuntimeException::class, function() {})
            ->ifNotThrown('custom message %s')
            ->__invoke()
        ;
    }

    /**
     * @test
     */
    public function can_customize_the_no_throw_message_with_custom_context(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('custom message 1');

        ThrowsAssertion::expect(\RuntimeException::class, function() {})
            ->ifNotThrown('custom message %s', [1])
            ->__invoke()
        ;
    }

    /**
     * @test
     */
    public function can_customize_the_mismatch_message(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('custom message');

        ThrowsAssertion::expect(\RuntimeException::class, function() { throw new \Exception(); })
            ->ifMismatch('custom message')
            ->__invoke()
        ;
    }

    /**
     * @test
     */
    public function can_customize_the_mismatch_message_with_default_context(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('custom message RuntimeException Exception');

        ThrowsAssertion::expect(\RuntimeException::class, function() { throw new \Exception(); })
            ->ifMismatch('custom message %s %s')
            ->__invoke()
        ;
    }

    /**
     * @test
     */
    public function can_customize_the_mismatch_message_with_custom_context(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('custom message 1 2');

        ThrowsAssertion::expect(\RuntimeException::class, function() { throw new \Exception(); })
            ->ifMismatch('custom message %s %s', [1, 2])
            ->__invoke()
        ;
    }

    /**
     * @test
     */
    public function can_add_on_call_callbacks(): void
    {
        $calls = 0;
        $arguments = [];
        $actualException = new \RuntimeException();
        $typeHintedCallback = function(\Throwable $e) use (&$calls, &$arguments) {
            ++$calls;
            $arguments[] = $e;
        };
        $unTypeHintedCallback = function() use (&$calls) {
            ++$calls;
        };

        ThrowsAssertion::expect($typeHintedCallback, function() use ($actualException) { throw $actualException; })
            ->onCatch($typeHintedCallback)
            ->onCatch($unTypeHintedCallback)
            ->__invoke()
        ;

        $this->assertSame(3, $calls);
        $this->assertCount(2, $arguments);
        $this->assertSame($arguments[0], $actualException);
        $this->assertSame($arguments[1], $actualException);
    }

    /**
     * @test
     */
    public function when_expected_is_callable_first_argument_is_required(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('When $exception is a callback, the first parameter must be type-hinted as the expected exception.');

        ThrowsAssertion::expect(function() {}, function() {});
    }

    /**
     * @test
     */
    public function when_expected_is_callable_first_argument_must_be_type_hinted(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('When $exception is a callback, the first parameter must be type-hinted as the expected exception.');

        ThrowsAssertion::expect(function($exception) {}, function() {});
    }
}
