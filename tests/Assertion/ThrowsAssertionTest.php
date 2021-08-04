<?php

namespace Zenstruck\Assert\Tests\Assertion;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert\Assertion\ThrowsAssertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ThrowsAssertionTest extends TestCase
{
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
